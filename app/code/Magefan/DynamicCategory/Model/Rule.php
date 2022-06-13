<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model;

/**
 * Class Rule
 */
class Rule extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Magefan\DynamicCategory\Model\ResourceModel\Rule::class);
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Dynamic Category Rules' : 'Dynamic Category Rule';
    }
}
