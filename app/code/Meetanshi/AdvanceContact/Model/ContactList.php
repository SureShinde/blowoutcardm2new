<?php

namespace Meetanshi\AdvanceContact\Model;

use Magento\Framework\Model\AbstractModel;

class ContactList extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Meetanshi\AdvanceContact\Model\ResourceModel\ContactList');
    }
}
