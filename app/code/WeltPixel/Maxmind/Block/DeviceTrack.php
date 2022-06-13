<?php
namespace WeltPixel\Maxmind\Block;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use WeltPixel\Maxmind\Helper\Data;

/**
 * Class DeviceTrack
 * @package WeltPixel\Maxmind\Block
 */
class DeviceTrack extends Template
{
    /**
     * @var Data|null
     */
    protected $_helper = null;

    /**
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_helper = $helper;
    }

    /**
     * @return bool
     */
    public function isTrackingEnabled()
    {
        $route = $this->getRoute();
        $controller = $this->getController();
        $isTrackingEnabled = $this->_helper->isDeviceTrackEnabled();
        $deviceTrackOn = $this->_helper->getDeviceTrackOn();
        $deviceTrackOnArray = explode(',', $deviceTrackOn);

        if ($this->_request->isAjax() || !$isTrackingEnabled) {
            return false;
        }

        $isTrackingEnabled = false;
        if (in_array($route, $this->getCheckoutRoutes()) ||
            ($route === 'cms') && (in_array('cms', $deviceTrackOnArray)) ||
            ($route === 'catalog') && (in_array($controller, $deviceTrackOnArray)) ||
            (!in_array($route, ['checkout', 'cms', 'catalog'])) && (in_array('other', $deviceTrackOnArray))
        ) {
            $isTrackingEnabled  = true;
        }

        return $isTrackingEnabled;
    }

    /**
     * @return string[]
     */
    public function getCheckoutRoutes()
    {
        return [
            'checkout'
        ];
    }

    /**
     * @return mixed
     */
    protected function getRoute()
    {
        return $this->_request->getRouteName();
    }

    /**
     * @return mixed
     */
    protected function getController()
    {
        return $this->_request->getControllerName();
    }
}
