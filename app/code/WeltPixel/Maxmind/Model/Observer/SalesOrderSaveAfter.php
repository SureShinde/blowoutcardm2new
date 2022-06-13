<?php

namespace WeltPixel\Maxmind\Model\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use WeltPixel\Maxmind\Helper\Data as MaxmindHelper;

class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     * @var MaxmindHelper
     */
    protected $_helper;

    /**
     * @var Registry|null
     */
    protected $_registry = null;

    /**
     * @var Monolog
     */
    protected $_monolog;

    /**
     * [__construct description].
     *
     * @param MaxmindHelper $helper
     * @param Registry $registry
     * @param Monolog $monolog
     */

    public function __construct(
        MaxmindHelper $helper,
        Registry $registry,
        Monolog $monolog
    ) {
        $this->_helper   = $helper;
        $this->_registry = $registry;
        $this->_monolog  = $monolog;
    }

    /**
     * Set Maxmind data
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
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

        $orderAmount = $order->getGrandTotal();
        $minOrderAmount = $this->_helper->getConfigValue('general/min_order_amount', $order->getStoreId());

        if ($minOrderAmount && ($orderAmount < $minOrderAmount)) {
            return $this;
        }

        $payment = $order->getPayment();
        $paymentMethod = $payment->getMethod();
        $allowedPaymentsMethods = $this->_helper->getAllowedPaymentMethods();

        if (!in_array($paymentMethod, $allowedPaymentsMethods)) {
            return $this;
        }

        try {
            if ($maxmindData = $order->getMaxmindData()) {
                if ($order->getId()) {
                    $this->_helper->saveMaxmindData($maxmindData, $order);
                    $order->unsMaxmindData();
                }
            }
        } catch (\Exception $e) {
            $this->_monolog->addError($e->getMessage());
        }
    }
}
