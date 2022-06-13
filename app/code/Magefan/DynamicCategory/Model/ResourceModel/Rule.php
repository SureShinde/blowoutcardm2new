<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Rule
 */
class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Rule constructor.
     * @param Context $context
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        Context $context,
        \Magento\CatalogRule\Model\RuleFactory  $ruleFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context);
    }


    protected function _construct()
    {
        $this->_init('magefan_dynamic_category_rule', 'id');
    }

     /**
      * @param \Magento\Framework\Model\AbstractModel $object
      * @return $this
      */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /* Conditions */
        if ($object->getRule('conditions')) {
            $rule = $this->ruleFactory->create();
            $rule->loadPost(['conditions' => $object->getRule('conditions')]);
            $rule->beforeSave();
            $object->setData(
                'conditions_serialized',
                $rule->getConditionsSerialized()
            );
        }

        /* Website IDs */
        if (is_array($object->getWebsiteIds())) {
            $object->setWebsiteIds(
                implode(',', $object->getWebsiteIds())
            );
        }

        /* Catalog Price Rule IDs */
        if (is_array($object->getCatalogPriceRuleIds())) {
            $object->setCatalogPriceRuleIds(
                implode(',', $object->getCatalogPriceRuleIds())
            );
        }

        /* Category IDs */
        $categoryIds = $object->getCategoryIds();
        if (!is_array($categoryIds)) {
            $categoryIds = explode(',', $categoryIds);
        }
        foreach ($categoryIds as $k => $categoryId) {
            if (!$categoryId) {
                unset($categoryIds[$k]);
            } else {
                try {
                    $category = $this->categoryRepository->get($categoryId);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    unset($categoryIds[$k]);
                }
            }
        }
        $object->setData('category_ids', implode(',', $categoryIds));

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->processArrayFields($object);
        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return null
     */
    public function processArrayFields($object)
    {
        if ($object->getCategoryIds()) {
            $object->setCategoryIds(
                explode(',', $object->getCategoryIds())
            );
        }

        if ($object->getWebsiteIds()) {
            $object->setWebsiteIds(
                explode(',', $object->getWebsiteIds())
            );
        }

        if ($object->getCatalogPriceRuleIds()) {
            $object->setCatalogPriceRuleIds(
                explode(',', $object->getCatalogPriceRuleIds())
            );
        }
    }
}
