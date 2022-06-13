<?php

namespace Meetanshi\AdvanceContact\Model\ResourceModel\ContactList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * @var string
     */
    protected $_eventPrefix = 'contact_attribute_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'contact_attribute_collection';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Meetanshi\AdvanceContact\Model\ContactList',
            'Meetanshi\AdvanceContact\Model\ResourceModel\ContactList'
        );
    }
}
