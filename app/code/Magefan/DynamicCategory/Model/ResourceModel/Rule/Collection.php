<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Magefan\DynamicCategory\Model\Rule::class,
            \Magefan\DynamicCategory\Model\ResourceModel\Rule::class
        );
    }

    /**
     * Redeclare after load method for specifying collection items original data
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as $object) {
            $this->getResource()->processArrayFields($object);
        }
        return $this;
    }
}
