<?php

namespace WeltPixel\Maxmind\Block\Adminhtml\Order\View\Tab;

use Magento\Framework\Serialize\Serializer\Serialize;
use WeltPixel\Maxmind\Model\Config\Source\ApiVersion;

class Maxmind extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/tab/maxmind.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_maxmindModel = null;

    protected $_maxmindData = null;

    /**
     * @var Serialize
     */
    protected $_serializer;

    /**
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_helper;

    /**
     * @var int
     */
    protected $apiVersion;

    /**
     * @var array
     */
    protected $sentData;

    /**
     * @var boolean
     */
    protected $chargebackFlag;

    /**
     * Maxmind constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \WeltPixel\Maxmind\Helper\Data $helper
     * @param \WeltPixel\Maxmind\Model\Maxmind $maxmind
     * @param Serialize $serializer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \WeltPixel\Maxmind\Helper\Data $helper,
        \WeltPixel\Maxmind\Model\Maxmind $maxmind,
        Serialize $serializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->_coreRegistry = $registry;
        $this->_maxmindModel = $maxmind;
        $this->_serializer = $serializer;
        $this->_helper = $helper;
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Retrieve maxmind fraud order data
     *
     * @return array
     */
    public function getMaxmindData()
    {
        if (empty($this->_maxmindData)) {
            $orderId = $this->getOrder()->getId();
            $model = $this->_maxmindModel;

            try {
                $model->loadMaxmindByOrderId($orderId);
                $this->apiVersion = $model->getApiVersion();
                $this->chargebackFlag = $model->getChargebackFlag();
                $this->sentData = $this->_serializer->unserialize(utf8_decode($model->getSentData()));

                if ($model->getFraudData()) {
                    $this->_maxmindData = $this->_serializer->unserialize(utf8_decode($model->getFraudData()));
                } else {
                    $this->_maxmindData = [];
                }
            } catch (\Exception $ex) {
                $this->_maxmindData = [];
            }
        }

        return $this->_maxmindData;
    }

    /**
     * Return the sys config score threshold
     * @return mixed
     */
    public function getScoreThreshold()
    {
        return $this->_helper->getConfigValue('general/score_threshold', $this->getOrder()->getStoreId());
    }

    /**
     * @return boolean
     */
    public function isChargeBackEnabled()
    {
        return $this->_helper->isChargebackEnabled($this->getOrder()->getStoreId());
    }

    /**
     * Get risk score percent
     *
     * @return string
     */
    public function getPercent()
    {
        $maxmindData    = $this->getMaxmindData();
        $percent = '';

        if (array_key_exists('riskScore', $maxmindData) && !empty($maxmindData['riskScore'])) {
            $fraudScore = number_format($maxmindData['riskScore'], 2);
            if ($fraudScore >= 0.1 && $fraudScore <= 4.99) {
                $percent = "90%";
            } elseif ($fraudScore >= 5 && $fraudScore <= 9.99) {
                $percent =  "5%";
            } elseif ($fraudScore >= 10 && $fraudScore <= 29.99) {
                $percent =  "3%";
            } elseif ($fraudScore >= 30 && $fraudScore <= 99.99) {
                $percent =  "2%";
            } else {
                $percent = '';
            }
        }

        if (array_key_exists('risk_score', $maxmindData) && !empty($maxmindData['risk_score'])) {
            $fraudScore = number_format($maxmindData['risk_score'], 2);
            if ($fraudScore >= 0.1 && $fraudScore <= 4.99) {
                $percent = "90%";
            } elseif ($fraudScore >= 5 && $fraudScore <= 9.99) {
                $percent =  "5%";
            } elseif ($fraudScore >= 10 && $fraudScore <= 29.99) {
                $percent =  "3%";
            } elseif ($fraudScore >= 30 && $fraudScore <= 99.99) {
                $percent =  "2%";
            } else {
                $percent = '';
            }
        }

        return $percent;
    }

    /**
     * Get risk score color
     *
     * @return string
     */
    public function getColor()
    {
        $maxmindData    = $this->getMaxmindData();
        $scoreThreshold = $this->getScoreThreshold();
        $fraudScore = 0;

        if (array_key_exists('riskScore', $maxmindData) && !empty($maxmindData['riskScore'])) {
            $fraudScore = number_format($maxmindData['riskScore'], 2);
        }

        if (array_key_exists('risk_score', $maxmindData) && !empty($maxmindData['risk_score'])) {
            $fraudScore = number_format($maxmindData['risk_score'], 2);
        }

        return $fraudScore >= $scoreThreshold ? 'red' : 'auto';
    }

    /**
     * Get maxmind fraud score
     *
     * @return string
     */
    public function getFraudScore()
    {
        $maxmindData = $this->getMaxmindData();
        $fraudScore  = null;

        if (array_key_exists('riskScore', $maxmindData) && !empty($maxmindData['riskScore'])) {
            $fraudScore = number_format($maxmindData['riskScore'], 2);
        }

        if (array_key_exists('risk_score', $maxmindData) && !empty($maxmindData['risk_score'])) {
            $fraudScore = number_format($maxmindData['risk_score'], 2);
        }

        return $fraudScore;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('MaxMind Fraud Detection');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order MaxMind');
    }

    /**
     * Get Tab Class
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax only';
    }

    /**
     * Get Class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->getTabClass();
    }

    /**
     * Get Tab Url
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('maxmind/*/report', ['_current' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        $this->getMaxmindData();
        if (!$this->apiVersion) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        $this->getMaxmindData();
        switch ($this->apiVersion) {
            case ApiVersion::MAXMIND_API_FACTORS:
                $template = 'order/view/tab/maxmind_factors.phtml';
                break;
            case ApiVersion::MAXMIND_API_INSIGHT:
                $template = 'order/view/tab/maxmind_insights.phtml';
                break;
            case ApiVersion::MAXMIND_API_SCORE:
                $template = 'order/view/tab/maxmind_score.phtml';
                break;
            case ApiVersion::MAXMIND_API_LEGACY:
            default:
                $template = 'order/view/tab/maxmind_legacy.phtml';
                break;
        }

        $this->setTemplate($template);
        return parent::getTemplate();
    }

    /**
     * @param string $rawData
     * @param bool $boolean
     * @return string
     */
    public function getDisplayValue($maxmindData, $rawData, $boolean = false)
    {
        if ($boolean) {
            return (isset($maxmindData[$rawData]) && !empty($maxmindData[$rawData]) && $maxmindData[$rawData])
                ? __('Yes')
                : __('No');
        }
        return (isset($maxmindData[$rawData]) && strlen($maxmindData[$rawData]))
            ? $maxmindData[$rawData]
            : "&ndash;";
    }

    /**
     * @param string $displayValue
     * @param string $flag
     * @return string string
     */
    public function getMessageErrorFlag($displayValue, $flag)
    {
        if (strtolower($displayValue) == $flag) {
            return 'success';
        }

        return 'error';
    }

    /**
     * @return bool
     */
    public function getChargeBackFlag()
    {
        return $this->chargebackFlag;
    }

    /**
     * @return array
     */
    public function getSentData()
    {
        return $this->sentData;
    }
}
