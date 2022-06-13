<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Plugin\Mui;

use Magefan\DynamicCategory\Controller\Adminhtml\Product\Mui\Render as RenderController;

/**
 * Class Render
 * @package Magefan\DynamicCategory\Plugin\Mui
 */
class Render
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magefan\DynamicCategory\Model\DynamicCategoryAction
     */
    protected $dynamicCategoryAction;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $catalogRuleFactory;

    /**
     * Render constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction
     * @param \Magento\CatalogRule\Model\RuleFactory $catalogRuleFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction,
        \Magento\CatalogRule\Model\RuleFactory  $catalogRuleFactory
    ) {
        $this->dynamicCategoryAction = $dynamicCategoryAction;
        $this->registry = $registry;
        $this->catalogRuleFactory = $catalogRuleFactory;
    }

    /**
     * @param RenderController $renderController
     */
    public function beforeExecute(RenderController $renderController)
    {
        $request = $renderController->getRequest();
        if ($conditions = $request->getParam('rule', null)) {
            $catalogRule = $this->catalogRuleFactory->create();
            $catalogRule->loadPost($conditions);
            $catalogRule->setWebsiteIds($request->getParam('website_ids', null));
            $catalogRule->setCatalogPriceRuleIds($request->getParam('catalog_price_rule_ids', null));

            $this->registry->register(
                \Magefan\DynamicCategory\UI\DataProvider\Product\ProductDataProvider::PRODUCTS_KEY,
                $this->dynamicCategoryAction->getListProductIds($catalogRule)
            );
        }
    }
}
