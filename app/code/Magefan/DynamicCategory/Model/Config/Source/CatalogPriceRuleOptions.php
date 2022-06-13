<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model\Config\Source;

use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class CatalogPriceRuleOptions
 */
class CatalogPriceRuleOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const ALL_PRICE_RULES_ID = 99999;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollection;

    /**
     * CatalogPriceRuleOptions constructor.
     * @param CollectionFactory $ruleCollection
     */
    public function __construct(
        CollectionFactory $ruleCollection
    ) {
        $this->ruleCollection = $ruleCollection;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Do not use'), 'value' => 0],
            ['label' => __('ALL rules'), 'value' => self::ALL_PRICE_RULES_ID]
        ];

        foreach ($this->ruleCollection->create() as $rule) {
            $this->_options[] = [
                'label' => $rule->getData('name') . ' (ID ' . $rule->getData('rule_id') . ')',
                'value' => $rule->getData('rule_id')
            ];
        }

        return $this->_options;
    }
}
