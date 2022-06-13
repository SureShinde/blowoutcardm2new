<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Plugin\Catalog\Controller\Adminhtml\Product;

use Magefan\DynamicCategory\Model\DynamicCategoryAction;
use Magefan\DynamicCategory\Model\Config;

/**
 * Class SavePlugin
 * @package Magefan\DynamicCategory\Plugin\Catalog\Controller\Adminhtml\Product
 */
class SavePlugin
{
    /**
     * @var DynamicCategoryAction
     */
    protected $dynamicCategoryAction;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SavePlugin constructor.
     * @param DynamicCategoryAction $dynamicCategoryAction
     * @param Config $config
     */
    public function __construct(
        DynamicCategoryAction $dynamicCategoryAction,
        Config $config
    ) {
        $this->dynamicCategoryAction = $dynamicCategoryAction;
        $this->config = $config;
    }

    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Save $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject, $result)
    {
        if ($this->config->isEnabled()) {
            $productId = $subject->getRequest()->getParam('id');
            if ($productId) {
                $this->dynamicCategoryAction->execute(['type' => 'product', 'product_id' => $productId]);
            }
        }

        return $result;
    }
}
