<?php

namespace WeltPixel\Maxmind\Model\ResourceModel\Maxmind;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Maxmind Collection
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */
class Collection extends AbstractCollection
{
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('WeltPixel\Maxmind\Model\Maxmind', 'WeltPixel\Maxmind\Model\ResourceModel\Maxmind');
    }
}
