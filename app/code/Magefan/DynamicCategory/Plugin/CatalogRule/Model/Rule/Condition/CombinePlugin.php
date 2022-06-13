<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Plugin\CatalogRule\Model\Rule\Condition;

use Magento\CatalogRule\Model\Rule\Condition\Combine;

/**
 * Class CombinePlugin
 */
class CombinePlugin
{
    /**
     * @var \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
     */
    private $productFactory;

    /**
     * @var \Magefan\DynamicCategory\Model\Rule\Condition\ProductFactory
     */
    private $customProductFactory;

    /**
     * @var \Magefan\DynamicCategory\Model\Rule\Condition\ProductDateFactory
     */
    private $customProductDateFactory;

    /**
     * CombinePlugin constructor.
     * @param \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $productFactory
     * @param \Magefan\DynamicCategory\Model\Rule\Condition\ProductFactory $customProductFactory
     * @param \Magefan\DynamicCategory\Model\Rule\Condition\ProductDateFactory|null $customProductDateFactory
     */
    public function __construct(
        \Magento\CatalogRule\Model\Rule\Condition\ProductFactory $productFactory,
        \Magefan\DynamicCategory\Model\Rule\Condition\ProductFactory $customProductFactory,
        \Magefan\DynamicCategory\Model\Rule\Condition\ProductDateFactory $customProductDateFactory = null
    ) {
        $this->productFactory = $productFactory;
        $this->customProductFactory = $customProductFactory;
        $this->customProductDateFactory = $customProductDateFactory ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magefan\DynamicCategory\Model\Rule\Condition\ProductDateFactory::class
            );
    }

    public function afterGetNewChildSelectOptions(Combine $subject, $result)
    {
        $productAttributes = $this->customProductFactory->create()->loadAttributeOptions()->getAttributeOption();
        $dynamicAttributes = [];
        foreach ($productAttributes as $code => $label) {
            $dynamicAttributes[] = [
                'value' => 'Magefan\DynamicCategory\Model\Rule\Condition\Product|' . $code,
                'label' => $label,
            ];
        }

        $productAttributes = $this->customProductDateFactory->create()->loadAttributeOptions()->getAttributeOption();
        foreach ($productAttributes as $code => $label) {
            $dynamicAttributes[] = [
                'value' => 'Magefan\DynamicCategory\Model\Rule\Condition\ProductDate|' . $code,
                'label' => $label,
            ];
        }

        $result = array_merge_recursive(
            $result,
            [
                ['label' => __('Custom Product Attributes'), 'value' => $dynamicAttributes],
            ]
        );
        
        return $result;
    }
}
