<?php

namespace Meetanshi\AdvanceContact\Model;

use Magento\Framework\Model\AbstractModel;

class AdvanceContact extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Meetanshi\AdvanceContact\Model\ResourceModel\AdvanceContact');
    }
}
