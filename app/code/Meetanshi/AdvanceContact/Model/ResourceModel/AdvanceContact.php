<?php

namespace Meetanshi\AdvanceContact\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AdvanceContact extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('meetanshi_contact_department', 'id');
    }
}
