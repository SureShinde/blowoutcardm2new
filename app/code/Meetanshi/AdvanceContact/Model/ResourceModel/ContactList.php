<?php

namespace Meetanshi\AdvanceContact\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ContactList extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('meetanshi_advance_contact', 'id');
    }
}
