<?php

namespace WeltPixel\Maxmind\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Maxmind Resource Model
 * @category WeltPixel
 * @package  WeltPixel_Maxmind
 * @module   Maxmind
 * @author   WeltPixel Developer
 */
class Maxmind extends AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('weltpixel_maxmind_data', 'id');
    }
}
