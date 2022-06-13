<?php

namespace WeltPixel\Maxmind\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Maxmind Model
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */
class Maxmind extends AbstractModel
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Maxmind\Model\ResourceModel\Maxmind');
    }

    /**
     * Load maxmind model by order id
     *
     * @param int $orderId
     * @return Maxmind
     */
    public function loadMaxmindByOrderId($orderId)
    {
        $this->load($orderId, 'order_id');

        return $this;
    }
}
