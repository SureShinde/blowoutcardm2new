<?php
namespace WeltPixel\Maxmind\Block\Adminhtml\Order\View;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Sales\Model\Order;
use WeltPixel\Maxmind\Helper\Data;
use WeltPixel\Maxmind\Model\Maxmind as MaxmindModel;

class Maxmindgrid extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Data|null
     */
    protected $_helper = null;

    /**
     * @var MaxmindModel
     */
    protected $maxmindModel;

    /**
     * @var Serialize
     */
    protected $serializer;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param MaxmindModel $maxmindModel
     * @param Serialize $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helper,
        MaxmindModel $maxmindModel,
        Serialize $serializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->_coreRegistry = $registry;
        $this->_helper       = $helper;
        $this->maxmindModel = $maxmindModel;
        $this->serializer = $serializer;
    }

    /**
     * Return the Maxmind fraud score
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getMaxmindFraudScore()
    {
        return $this->getOrder()->getData('weltpixel_fraud_score');
    }

    public function isChargebackEnabled()
    {
        return $this->_helper->isChargebackEnabled($this->getOrder()->getStoreId());
    }

    /**
     * Return the sys config score threshold
     *
     * @return mixed
     */
    public function getScoreThreshold()
    {
        return $this->_helper->getConfigValue('general/score_threshold', $this->getOrder()->getStoreId());
    }

    /**
     * Return the Maxmind remaining credit
     *
     * @return mixed
     */
    public function getRemainingCredit()
    {
        return $this->_helper->getRemainingCredit();
    }

    /**
     * Retrieve available order
     *
     * @return Order
     * @throws LocalizedException
     */
    public function getOrder()
    {
        if ($this->hasOrder()) {
            return $this->getData('order');
        }
        if ($this->_coreRegistry->registry('current_order')) {
            return $this->_coreRegistry->registry('current_order');
        }
        if ($this->_coreRegistry->registry('order')) {
            return $this->_coreRegistry->registry('order');
        }
        throw new LocalizedException(__('We can\'t get the order instance right now.'));
    }

    /**
     * @return mixed|string
     * @throws LocalizedException
     */
    public function getReportedIp()
    {
        $orderId = $this->getOrder()->getStoreId();
        $this->maxmindModel->loadMaxmindByOrderId($orderId);

        try {
            $sentData = $this->serializer->unserialize($this->maxmindModel->getSentData());
        } catch (\Exception $ex) {
            $sentData = [];
        }
        $apiVersion = $this->maxmindModel->getApiVersion();
        $reportedApiAddress = '';

        switch ($apiVersion) {
            case 2:
            case 3:
            case 4:
            $reportedApiAddress = isset($sentData['device']['ip_address']) ? $sentData['device']['ip_address'] : '';
                break;
            default:
                $reportedApiAddress= isset($sentData['i']) ? $sentData['i'] : '';
                break;
        }

        return $reportedApiAddress;
    }
}
