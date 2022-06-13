<?php

namespace Meetanshi\AdvanceContact\Model\ResourceModel\AdvanceContact;

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
    protected $_eventPrefix = 'department_attribute_collection';
    /**
     * @var string
     */
    protected $_eventObject = 'department_attribute_collection';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Meetanshi\AdvanceContact\Model\AdvanceContact',
            'Meetanshi\AdvanceContact\Model\ResourceModel\AdvanceContact'
        );
    }
}
