<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WebSites
 */
class WebSites implements OptionSourceInterface
{
    /**
     * @var
     */
    protected $storeManger;

    /**
     * WebSites constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }
    /**
     * Get websites as id => name associative array
     *
     * @param bool $withDefault
     * @param string $attribute
     * @return array
     */
    public function getWebsiteOptionHash($withDefault = false, $attribute = 'name')
    {
        $options = [];
        foreach ($this->storeManager->getWebsites((bool)$withDefault && $this->_isAdminScopeAllowed) as $website) {
            $options[$website->getId()] = $website->getDataUsingMethod($attribute);
        }
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $availableOptions = $this->getWebsiteOptionHash();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
