<?php

namespace WeltPixel\Maxmind\Model\Observer;

use Codeception\Template\Api;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;
use WeltPixel\Maxmind\Helper\Data as MaxmindHelper;
use WeltPixel\Maxmind\Model\Api\Score as MaxmindScore;
use WeltPixel\Maxmind\Model\Api\Insights as MaxmindInsights;
use WeltPixel\Maxmind\Model\Api\Factors as MaxmindFactors;
use WeltPixel\Maxmind\Model\Config\Source\ApiVersion;

class SalesOrderBeforeSave implements ObserverInterface
{
    const XML_PATH_EMAIL_SENDER_IDENTITY = 'weltpixel_maxmind_config/general/email_sender_identity';

    public static $maxmindErrors = [
        'FATAL_ERR',
        'INVALID_LICENSE_KEY',
        'MAX_REQUESTS_PER_LICENSE',
        'IP_REQUIRED',
        'LICENSE_REQUIRED',
        'COUNTRY_REQUIRED',
        'MAX_REQUESTS_REACHED',
        'SYSTEM_ERROR',
        'IP_NOT_FOUND'
    ];

    /**
     * @var MaxmindHelper
     */
    private $_helper;

    /**
     * @var Registry|null
     */
    private $_registry = null;

    /**
     * @var Monolog
     */
    private $_monolog;

    /** @var  TransportInterfaceFactory */
    protected $_mailTransportFactory;

    /** @var  MessageInterface */
    protected $_mailMessage;

    /**
     * @var MaxmindScore
     */
    protected $maxmindScore;

    /**
     * @var MaxmindInsights
     */
    protected $maxmindInsights;

    /**
     * @var MaxmindFactors
     */
    protected $maxmindFactors;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SenderResolverInterface
     */
    protected $senderResolver;

    /**
     * [__construct description].
     *
     * @param MaxmindHelper $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param SenderResolverInterface $sernderResolver
     * @param Registry $registry
     * @param Monolog $monolog
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param MessageInterface $mailMessage
     * @param MaxmindScore $maxmindScore
     * @param MaxmindInsights $maxmindInsights
     * @param MaxmindFactors $maxmindFactors
     */
    public function __construct(
        MaxmindHelper $helper,
        ScopeConfigInterface $scopeConfig,
        SenderResolverInterface $sernderResolver,
        Registry $registry,
        Monolog $monolog,
        TransportInterfaceFactory $mailTransportFactory,
        MessageInterface $mailMessage,
        MaxmindScore $maxmindScore,
        MaxmindInsights $maxmindInsights,
        MaxmindFactors $maxmindFactors
    ) {
        $this->_helper   = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->senderResolver = $sernderResolver;
        $this->_registry = $registry;
        $this->_monolog  = $monolog;
        $this->_mailTransportFactory = $mailTransportFactory;
        $this->_mailMessage = $mailMessage;
        $this->maxmindScore = $maxmindScore;
        $this->maxmindInsights = $maxmindInsights;
        $this->maxmindFactors = $maxmindFactors;
    }

    /**
     * Set Maxmind data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        if (!$this->_helper->moduleEnabled($order->getStoreId())) {
            return $this;
        }

        /** For orders from admin or no ip address*/
        if (!$order->getRemoteIp()) {
            return $this;
        }

        $fraudScoreData = $order->getWeltpixelFraudScore();

        $orderAmount = $order->getGrandTotal();
        $minOrderAmount = $this->_helper->getConfigValue('general/min_order_amount', $order->getStoreId());
        $apiVersion = $this->_helper->getApiVersion($order->getStoreId());

        if ($minOrderAmount && ($orderAmount < $minOrderAmount) || !empty($fraudScoreData)) {
            return $this;
        }

        $origdata = $order->getOrigData();
        $originalState = null;
        if ($origdata) {
            $originalState = array_key_exists('state', $origdata) ? $origdata['state'] : null;
        }

        if ($order->isCanceled() || $order->getState() == Order::STATE_CLOSED
            || ($originalState && $originalState == Order::STATE_HOLDED)) {
            return $this;
        }

        $payment = $order->getPayment();
        $paymentMethod = $payment->getMethod();
        $allowedPaymentsMethods = $this->_helper->getAllowedPaymentMethods();

        if (!in_array($paymentMethod, $allowedPaymentsMethods)) {
            return $this;
        }

        try {

            switch ($apiVersion) {
                case ApiVersion::MAXMIND_API_FACTORS:
                    $maxmindData = $this->maxmindFactors->getMaxmindData($order);
                    break;
                case ApiVersion::MAXMIND_API_INSIGHT:
                    $maxmindData = $this->maxmindInsights->getMaxmindData($order);
                    break;
                case ApiVersion::MAXMIND_API_SCORE:
                    $maxmindData = $this->maxmindScore->getMaxmindData($order);
                    break;
                case ApiVersion::MAXMIND_API_LEGACY:
                default:
                    $maxmindData = $this->_helper->getMaxmindData($payment, $order->getStoreId());
                    $maxmindData = $this->_helper->checkMaxmindData($maxmindData);
                    break;
            }

            if (!isset($maxmindData['err']) || !in_array($maxmindData['err'], self::$maxmindErrors)) {
                $this->_helper->saveMaxmindData($maxmindData, $order);

                $fraudScore = array_key_exists('ourscore', $maxmindData) ? $maxmindData['ourscore'] : '';

                $order->setWeltpixelFraudScore($fraudScore);
                $order->setMaxmindData($maxmindData);

                $ourscore = array_key_exists('ourscore', $maxmindData) ? $maxmindData['ourscore'] : null;

                if (!$ourscore) {
                    return $this;
                }

                $canHold = $this->_helper->canHold($ourscore, $order->getStoreId());

                if ($order->canHold() && $canHold) {
                    $order->hold();
                    $this->sendEmailNotification($order->getIncrementId(), $order->getStoreId());
                }
            } else {
                $this->_monolog->addError("Maxmind Error: " . $maxmindData['err'] . ' ' . $maxmindData['errmsg']);
            }
        } catch (\Exception $e) {
            $this->_monolog->addError($e->getMessage());
        }

        return $this;
    }

    /**
     * @param string $orderIncrementId
     * @param int|null $storeId
     */
    public function sendEmailNotification($orderIncrementId, $storeId)
    {
        $sendEmailOnHold = $this->_helper->getSendEmailOnHold($storeId);
        $emailAddress = $this->_helper->getEmailAddressOnHold($storeId);
        $emailSubject = $this->_helper->getEmailSubjectOnHold($storeId);
        $emailContent = str_replace("{{ORDERNUMBER}}", $orderIncrementId, $this->_helper->getEmailContentOnHold($storeId));

        if (!$sendEmailOnHold) {
            return;
        }

        $emailSenderResult = $this->senderResolver->resolve($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER_IDENTITY, ScopeInterface::SCOPE_STORE));

        $this->_mailMessage->setMessageType(MessageInterface::TYPE_HTML)
            ->addTo($emailAddress)
            ->setBody($emailContent)
            ->setSubject($emailSubject)
            ->setFromAddress(
                $emailSenderResult['email'],
                $emailSenderResult['name']
            );


        $mailTransport = $this->_mailTransportFactory->create(['message' => $this->_mailMessage]);
        try {
            $mailTransport->sendMessage();
        } catch (\Exception $e) {
            $this->_monolog->addError($e->getMessage());
        }
    }
}
