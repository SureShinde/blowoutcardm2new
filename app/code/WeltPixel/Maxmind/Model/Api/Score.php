<?php

namespace WeltPixel\Maxmind\Model\Api;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\HTTP\Header as HttpHeader;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Session\SessionManager;
use Magento\Sales\Model\Order;
use Magento\Variable\Model\Variable;
use WeltPixel\Maxmind\Helper\Data as MaxmindHelper;
use WeltPixel\Maxmind\Model\Maxmind as MaxmindModel;

/**
 * Class Score
 * @package WeltPixel\Maxmind\Model\Api
 */
class Score extends AbstractModel
{

    /**
     * @var MaxmindHelper
     */
    protected $maxmindHelper;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var HttpHeader
     */
    protected $httpHeader;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var string
     */
    protected $basePath = '/minfraud/v2.0/';

    /**
     * @var string
     */
    protected $apiType = 'score';

    /**
     * @var string
     */
    protected $licenseKey;

    /**
     * @var string
     */
    protected $accountId;

    /**
     * @var string
     */
    protected $forcedIp;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @var MaxmindModel
     */
    protected $maxmindModel;

    /**
     * @var Serialize
     */
    protected $serializer;

    /**
     * @var Variable
     */
    private $variableModel;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param MaxmindHelper $maxmindHelper
     * @param SessionManager $sessionManager
     * @param HttpHeader $httpHeader
     * @param CategoryFactory $categoryFactory
     * @param MaxmindModel $maxmindModel
     * @param Serialize $serializer
     * @param Variable $variableModel
     */
    public function __construct(
        Context $context,
        Registry $registry,
        MaxmindHelper $maxmindHelper,
        SessionManager $sessionManager,
        HttpHeader $httpHeader,
        CategoryFactory $categoryFactory,
        MaxmindModel $maxmindModel,
        Serialize $serializer,
        Variable $variableModel
    ) {
        parent::__construct($context, $registry);
        $this->maxmindHelper = $maxmindHelper;
        $this->sessionManager = $sessionManager;
        $this->httpHeader = $httpHeader;
        $this->categoryFactory = $categoryFactory;
        $this->maxmindModel = $maxmindModel;
        $this->serializer = $serializer;
        $this->variableModel = $variableModel;
    }

    /**
     * @return string
     */
    public function getApiBasePath()
    {
        return $this->basePath;
    }

    /**
     * @return string
     */
    public function getApiType()
    {
        return $this->apiType;
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getApiEndpoint($storeId)
    {
        return "https://" . $this->maxmindHelper->getApiEndpoint($storeId) . $this->getApiBasePath() . $this->getApiType();
    }

    /**
     * @param Order $order
     * @return array
     */
    public function getMaxmindData($order)
    {
        $storeId = $order->getStoreId();
        $this->licenseKey = $this->maxmindHelper->getLicenseKey($storeId);
        $this->accountId = $this->maxmindHelper->getAccountId($storeId);
        $this->forcedIp = $this->maxmindHelper->getForceIp($storeId);
        $this->order = $order;

        if (empty($this->licenseKey)) {
            return [
                "errmsg" => __("Maxmind License key not set."),
                "err" => "FATAL_ERR"
            ];
        }

        if (empty($this->accountId)) {
            return [
                "errmsg" => __("Maxmind Account Id is not set."),
                "err" => "FATAL_ERR"
            ];
        }

        $deviceOptions = $this->_getDeviceOptions();
        $ipAddress = $deviceOptions['ip_address'];
        $ipExceptions = $this->maxmindHelper->getIpExceptions($storeId);
        if ($ipExceptions) {
            $ipExceptions = explode(',', $ipExceptions);
            if (in_array($ipAddress, $ipExceptions)) {
                return [
                    "errmsg" => sprintf(
                        __("IP address %s is in IP exceptions list."),
                        $ipAddress
                    ),
                    "err" => "FATAL_ERR"
                ];
            }
        }

        $maxmindRequestData = [
            'device' => $deviceOptions,
            'event' => $this->_getEventOptions(),
            'email' => $this->_getEmailOptions(),
            'billing' => $this->_getBillingOptions(),
            'shipping' => $this->_getShippingOptions(),
            'order' => $this->_getOrderOptions(),
            'shopping_cart' =>$this->_getShoppingCartOptions()
        ];

        $requestBody = json_encode($maxmindRequestData);
        if ($requestBody === false) {
            return [
                "errmsg" => __('Request Api Json Encoding Error'),
                "err" => "FATAL_ERR"
            ];
        }

        $this->_beforeApiCall($maxmindRequestData);
        $result = $this->_makeApiCall($this->getApiEndpoint($storeId), $requestBody);
        $this->_afterApiCall($result);

        return $result;
    }

    /**
     * @return array
     */
    protected function _getDeviceOptions()
    {
        return [
            'ip_address' => $this->forcedIp ? $this->forcedIp : $this->maxmindHelper->getOrderRemoteIp($this->order),
            'user_agent' => $this->httpHeader->getHttpUserAgent(),
            'accept_language' => $this->httpHeader->getHttpAcceptLanguage(),
            'session_id' => $this->sessionManager->getSessionId()
        ];
    }

    /**
     * @return array
     */
    protected function _getEventOptions()
    {
        return [
            'transaction_id' => $this->order->getIncrementId(),
            'type' => 'purchase'
        ];
    }

    /**
     * @return array
     */
    protected function _getEmailOptions()
    {
        $emailAddress = $this->order->getCustomerEmail();
        $emailDomain = '';
        if (preg_match("/[^@]+@(.*)/", $emailAddress, $matchEmail)) {
            $emailDomain = $matchEmail[1];
        }

        return [
                'address' => hash('MD5', (strtolower($emailAddress))),
            'domain' => $emailDomain
        ];
    }

    /**
     * @return array
     */
    protected function _getBillingOptions()
    {
        $billingOptions = [];
        $billingAddress = $this->order->getBillingAddress();
        if ($billingAddress) {
            $billingOptions = [
                'first_name' => $billingAddress->getFirstname(),
                'last_name' => $billingAddress->getLastname(),
                'company' => $billingAddress->getCompany(),
                'city' => $billingAddress->getCity(),
                'region' => $billingAddress->getRegionCode(),
                'country' => $billingAddress->getCountryId(),
                'postal' => $billingAddress->getPostcode(),
                'phone_number' => $billingAddress->getTelephone(),
                'address' => $billingAddress->getStreet()
            ];

            $street = $billingAddress->getStreet();
            if (isset($street[0])) {
                $billingOptions['address'] = $street[0];
            }
            if (isset($street[1])) {
                $billingOptions['address_2'] = $street[1];
            }
        }

        foreach ($billingOptions as  $key => $billingOption) {
            if (empty(trim($billingOption))) {
                unset($billingOptions[$key]);
            }
        }

        return $billingOptions;
    }

    protected function _getShippingOptions()
    {
        $shippingOptions = [];
        $shippingAddress = $this->order->getShippingAddress();
        if ($shippingAddress) {
            $shippingOptions = [
                'first_name' => $shippingAddress->getFirstname(),
                'last_name' => $shippingAddress->getLastname(),
                'company' => $shippingAddress->getCompany(),
                'city' => $shippingAddress->getCity(),
                'region' => $shippingAddress->getRegionCode(),
                'country' => $shippingAddress->getCountryId(),
                'postal' => $shippingAddress->getPostcode(),
                'phone_number' => $shippingAddress->getTelephone(),
            ];

            $street = $shippingAddress->getStreet();
            if (isset($street[0])) {
                $shippingOptions['address'] = $street[0];
            }
            if (isset($street[1])) {
                $shippingOptions['address_2'] = $street[1];
            }
        }

        foreach ($shippingOptions as  $key => $shippingOption) {
            if (empty(trim($shippingOption))) {
                unset($shippingOptions[$key]);
            }
        }

        return $shippingOptions;
    }

    /**
     * @return array
     */
    protected function _getOrderOptions()
    {
        $orderOptions = [
            'amount' => $this->order->getGrandTotal(),
            'currency' => $this->order->getOrderCurrencyCode()
        ];

        if ($this->order->getCouponCode()) {
            $orderOptions['discount_code'] = $this->order->getCouponCode();
        }

        return $orderOptions;
    }

    /**
     * @return array
     */
    protected function _getShoppingCartOptions()
    {
        $categoryNames = [];
        $shoppingCartOptions = [];
        foreach ($this->order->getAllVisibleItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $productCategoryIds = $item->getProduct()->getCategoryIds();
            $categId = end($productCategoryIds);
            $categName = '';
            if ($categId) {
                if (isset($categoryNames[$categId])) {
                    $categName = $categoryNames[$categId];
                } else {
                    $category = $this->categoryFactory->create()->load($categId);
                    $categName = $category->getName();
                    $categoryNames[$categId] = $categName;
                }
            }
            $itemOptions = [
                'item_id' => $item->getSku(),
                'quantity' => $item->getQtyOrdered(),
                'price' => $item->getPrice()
            ];

            if ($categName) {
                $itemOptions['category'] = $categName;
            }
            $shoppingCartOptions[] = $itemOptions;
        }

        return $shoppingCartOptions;
    }

    /**
     * @param $maxmindRequestData
     * @throws \Exception
     */
    protected function _beforeApiCall($maxmindRequestData)
    {
        if ($this->order->getId()) {
            $this->maxmindModel->loadMaxmindByOrderId($this->order->getId())
                ->addData([
                    'order_id' => $this->order->getId(),
                    'sent_data' => utf8_encode($this->serializer->serialize($maxmindRequestData))
                ])
                ->save();
        } else {
            $this->order->setMaxmindTempData(utf8_encode($this->serializer->serialize($maxmindRequestData)));
        }
    }

    protected function _makeApiCall($apiEndpoint, $requestBody)
    {
        try {
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                [
                    'Authorization: Basic '
                    . base64_encode($this->accountId . ':' . $this->licenseKey),
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($requestBody)]
            );
            $result = curl_exec($ch);
            $result = json_decode($result, true);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } catch (\Exception $ex) {
            return [
                "errmsg" => __('Api Request Error ') . $ex->getMessage(),
                "err" => "FATAL_ERR"
            ];
        }

        if (($this->apiType == 'chargeback') && ($responseCode == 204)) {
            return [
                'msg' => __('Report Chargeback sent successfully')
            ];
        }

        if ($responseCode != 200) {
            return [
                "errmsg" => __('Api Request Error ') . $result['code'] . ' ' . $result['error'],
                "err" => "FATAL_ERR"
            ];
        }

        return $result;
    }

    /**
     * @param $result
     */
    protected function _afterApiCall(&$result)
    {
        if (!empty($result['queries_remaining'])) {
            $model = $this->variableModel->loadByCode(MaxmindHelper::MAXMIND_VARIABLE_NAME);

            if (!$model->getCode()) {
                $model->setCode(MaxmindHelper::MAXMIND_VARIABLE_NAME);
                $model->setName('Maxmind Remaining Credit ');
                $model->setPlainValue($result['queries_remaining']);
            } else {
                $model->setPlainValue($result['queries_remaining']);
            }
            $model->save();
        }

        if (array_key_exists('risk_score', $result) && $result['risk_score'] != '') {
            $result['ourscore'] = floatval($result['risk_score']);
        } else {
            $result['err'] = (array_key_exists('err', $result) && !empty($result['err']))
                ? $result['err']
                : "FATAL_ERR";
            $result['errmsg'] = array_key_exists('errmsg', $result)
                ? $result['errmsg']
                : __("No riskScore or server response.");
        }
    }
}
