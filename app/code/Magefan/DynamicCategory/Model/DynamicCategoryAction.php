<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model;

use Magefan\DynamicCategory\Model\Config\Source\CatalogPriceRuleOptions;

/**
 * Class DynamicCategoryAction
 */
class DynamicCategoryAction
{

    const ORDER_BY_ASC = 'ASC';

    /**
     * Product table
     */
    const CATALOG_CATEGORY_PRODUCT_TABLE_NAME = 'catalog_category_product';

    /**
     * @var \Magefan\DynamicCategory\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollection;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $iterator;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var
     */
    protected $productIds;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_productPriceIndexerProcessor;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    private $websitesMap;

    /**
     * DynamicCategoryAction constructor.
     * @param ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Model\ResourceModel\Iterator $iterator
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor
     * @param \Magento\Framework\Stdlib\DateTime\DateTime|null $dateTime
     */
    public function __construct(
        \Magefan\DynamicCategory\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Model\ResourceModel\Iterator $iterator,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogRule\Model\RuleFactory  $ruleFactory,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Catalog\Model\Indexer\Product\Price\Processor $productPriceIndexerProcessor,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime = null
    ) {
        $this->ruleCollection = $ruleCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryRepository = $categoryRepository;
        $this->iterator = $iterator;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->ruleFactory = $ruleFactory;
        $this->indexerFactory = $indexerFactory;
        $this->_productPriceIndexerProcessor = $productPriceIndexerProcessor;
        $this->dateTime = $dateTime ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
    }

    public function execute($params = [])
    {
        $rules = [];
        $productsFromCategory = [];

        $ruleCollection = $this->ruleCollection->create()
            ->addFieldToFilter('status', 1)
            ->setOrder('priority', self::ORDER_BY_ASC);
        if (count($ruleCollection)) {
            foreach ($ruleCollection as $item) {
                if ($conditionsSerialized = $item->getData('conditions_serialized')) {
                    $rule = $this->ruleFactory->create();
                    $rule->setData('conditions_serialized', $conditionsSerialized);
                    $rule->setData('remove_other', $item->getData('remove_other'));
                    $rule->setData('remove_products_from_other_categories', $item->getData('remove_products_from_other_categories'));
                    $rule->setData('real_item_id', $item->getId());
                    $rule->setWebsiteIds($item->getWebsiteIds());
                    $rule->setCatalogPriceRuleIds($item->getCatalogPriceRuleIds());
                    $rule->loadPost($rule->getData());

                    $conditions = $rule->getConditions();
                    if (empty($conditions['conditions'])) {
                        continue;
                    }

                    $categoryIds = $item->getCategoryIds();
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

                    if (!count($categoryIds)) {
                        continue;
                    }

                    foreach ($categoryIds as $categoryId) {
                        if (!isset($productsFromCategory[$categoryId])) {
                            $products = $this->productCollectionFactory->create()
                                ->addCategoriesFilter(['eq' => $categoryId]);
                            $productsFromCategory[$categoryId] = $products->getAllIds();
                        }
                    }

                    $rule->setCategoryIds($categoryIds);

                    $rules[] = $rule;
                }
            }
        }


        if (count($rules)) {
            $data = [];
            $connection = $ruleCollection->getResource();
            $catalogCategoryProductTable = $connection->getTable(self::CATALOG_CATEGORY_PRODUCT_TABLE_NAME);

            $productIdsForReindex = [];
            $categoryIdsForReindex = [];
            foreach ($rules as $rule) {
                $categoryIds = $rule->getCategoryIds();

                $productsFromRule = $this->getListProductIds($rule, $params);

                $removeOther = $rule->getData('remove_other');
                $removeProductsFromOtherCategories = $rule->getData('remove_products_from_other_categories');

                try {

                    if ($removeOther) {
                        $connection->beginTransaction();
                        $queryParams = ['category_id IN (?)' => $categoryIds];
                        if ($this->checkItFromProductPage($params)) {
                            $queryParams['product_id IN (?)'] = $productsFromRule;
                        }
                        $connection->getConnection()->delete(
                            $catalogCategoryProductTable,
                            $queryParams
                        );
                        $connection->commit();

                        /* Remove products from data array */
                        foreach ($data as $key => $value) {
                            foreach ($categoryIds as $categoryId) {
                                if (strpos($key, $categoryId . '-') === 0) {
                                    if ($this->checkItFromProductPage($params)) {
                                        foreach ($productsFromRule as $productId) {
                                            if (strpos($key, '-' . $productId) === (strlen($key) - strlen('-' . $productId))) {
                                                unset($data[$key]);
                                            }
                                        }
                                    } else {
                                        unset($data[$key]);
                                    }
                                }
                            }
                        }
                    }

                    if ($removeProductsFromOtherCategories && $productsFromRule) {
                        $connection->beginTransaction();
                        $connection->getConnection()->delete(
                            $catalogCategoryProductTable,
                            ['product_id IN (?)' => $productsFromRule]
                        );
                        $connection->commit();

                        /* Remove products from data array */
                        foreach ($data as $key => $value) {
                            foreach ($productsFromRule as $productId) {
                                if (strpos($key, '-' . $productId) === (strlen($key) - strlen('-' . $productId))) {
                                    unset($data[$key]);
                                }
                            }
                        }
                    }

                } catch (\Exception $e) {
                    $connection->rollBack();
                }
                if ($productsFromRule) {
                    foreach ($categoryIds as $categoryId) {
                        foreach ($productsFromRule as $productId) {
                            if ($removeProductsFromOtherCategories || $removeOther || !in_array($productId, $productsFromCategory[$categoryId])) {
                                $productIdsForReindex[] = $productId;
                                $categoryIdsForReindex[] = $categoryId;
                                $data[$categoryId . '-' . $productId] = [
                                    'category_id' => $categoryId,
                                    'product_id' => $productId
                                ];
                            }
                        }
                    }
                }
            }

            $count = count($data);

            if ($count) {
                try {
                    $connection->beginTransaction();
                    $connection->getConnection()->insertMultiple($catalogCategoryProductTable, $data);
                    $connection->commit();

                    /*
                    foreach (['catalog_product_category', 'catalog_category_product'] as $key) {
                        $indexer = $this->indexerFactory->create();
                        $indexer->load($key);
                        $indexer->reindexAll();
                    }
                    */

                    $this->indexerFactory->create()->load('catalog_product_category')
                        ->reindexList($productIdsForReindex, true);
                    $this->indexerFactory->create()->load('catalog_category_product')
                        ->reindexList($categoryIdsForReindex, true);

                    //$this->_productPriceIndexerProcessor->reindexList($productIdsForReindex, true);
                    // new solution to fix error price_tmp table is full
                    if (count($productIdsForReindex)) {
                        $productIdsForReindex = array_chunk($productIdsForReindex, 5);
                        foreach ($productIdsForReindex as $idsForReindex) {
                            $this->_productPriceIndexerProcessor->reindexList($idsForReindex, true);
                        }
                    }

                } catch (\Exception $e) {
                    $connection->rollBack();
                }
            }
        }
    }

    /**
     * @param $params
     * @return bool
     */
    protected function checkItFromProductPage($params)
    {

        return $params && $params['type'] == 'product';
    }

    /**
     * @param $rule
     * @param null $params
     * @return array
     */
    public function getListProductIds($rule, $params = null)
    {
        $this->productIds = [];

        if ($rule->getWebsiteIds()) {
            $storeIds = [];
            $websites = $this->_getWebsitesMap();
            foreach ($websites as $websiteId => $defaultStoreId) {
                if (in_array($websiteId, $rule->getWebsiteIds())) {
                    $storeIds[] = $defaultStoreId;
                }
            }
        } else {
            $storeIds = [0];
        }

        foreach ($storeIds as $storeId) {
            $productCollection = $this->productCollectionFactory->create();
            if ($storeId) {
                $productCollection->setStoreId($storeId);
            }

            if ($this->checkItFromProductPage($params)) {
                $productCollection
                    ->addFieldToFilter('entity_id', $params['product_id']);
            }

            if ($rule->getWebsiteIds()) {
                $productCollection->addWebsiteFilter($rule->getWebsiteIds());
            }

            /* Fix for quantity_and_stock_status attribute */
            $productCollection->getSelect()->joinLeft(
                ["csi" => $productCollection->getTable('cataloginventory_stock_item')],
                "csi.product_id = e.entity_id AND csi.website_id = 0",
                ["mfdyc_tmp_is_in_stock" => "csi.is_in_stock"]
            );
            $productCollection->getSelect()->group('e.entity_id');
            /* end */


            $rule->setCollectedAttributes([]);
            $rule->getConditions()->collectValidatedAttributes($productCollection);

            $parameters = [
                'attributes' => $rule->getCollectedAttributes(),
                'product' => $this->productFactory->create(),
                'rule' => $rule,
                'storeId' => $storeId,
            ];

            $this->iterator->walk(
                $productCollection->getSelect(),
                [[$this, 'callbackValidateProduct']],
                $parameters
            );
        }

        $this->productIds = array_merge(
            $this->productIds,
            $this->getListProductIdsFromCatalogPriceRule($rule, $params)
        );

        return array_unique($this->productIds);
    }


    /**
     * @param $rule
     * @param null $params
     * @return array
     */
    public function getListProductIdsFromCatalogPriceRule($rule, $params = null)
    {
        $productIds = [];
        $websiteIds = $rule->getWebsiteIds();
        $priceRulesIds = $rule->getCatalogPriceRuleIds();
        if ($priceRulesIds && !in_array(0, $priceRulesIds)) {
            $catalogRules = $this->ruleFactory->create()->getCollection();
            if (!in_array(CatalogPriceRuleOptions::ALL_PRICE_RULES_ID, $priceRulesIds)) {
                $catalogRules->addFieldToFilter('rule_id', ['in' => $priceRulesIds]);
            }

            $nowDate = $this->dateTime->gmtDate('Y-m-d');
            $catalogRules
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter(
                    ['from_date', 'from_date'],
                    [
                        ['null' => true],
                        ['lteq' => $nowDate]
                    ]
                )
                ->addFieldToFilter(
                    ['to_date', 'to_date'],
                    [
                        ['null' => true],
                        ['gteq' => $nowDate]
                    ]
                );


            foreach ($catalogRules as $catalogRule) {
                if (in_array(0, $catalogRule->getData('customer_group_ids'))) {
                    foreach ($catalogRule->getMatchingProductIds() as $productId => $websiteInfo) {
                        if (!$websiteIds) {
                            $allowed = true;
                        } else {
                            $allowed = false;
                            foreach ($websiteInfo as $websiteId => $status) {
                                if ($status && in_array($websiteId, $websiteIds)) {
                                    $allowed = true;
                                    break;
                                }
                            }
                        }

                        if ($allowed) {
                            $productIds[] = $productId;
                        }
                    }
                }
            }
        }

        if (isset($params['product_id'])) {
            if (in_array($params['product_id'], $productIds)) {
                $productIds = [$params['product_id']];
            } else {
                $productIds = [];
            }
        }

        /* Fix for configurable, bundle, grouped */
        if ($productIds) {
            $connection = $rule->getResource()->getConnection();
            $tableName = $rule->getResource()->getTable('catalog_product_relation');

            $productTable = $rule->getResource()->getTable('catalog_product_entity');
            $entityIdColumn = $connection->tableColumnExists($productTable, 'row_id') ? 'row_id' : 'entity_id';

            $select = $connection->select()
                ->from(['main_table' => $tableName])
                ->join(
                    ['e' => $productTable],
                    'e.' . $entityIdColumn . ' = main_table.parent_id',
                    ['e.entity_id']
                )
                ->where('main_table.child_id IN (?)', $productIds)
                ->where('e.entity_id IS NOT NULL');

            foreach ($connection->fetchAll($select) as $related) {
                $productIds[] = $related['entity_id'];
            }
        }
        /* End fix */

        return $productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param array $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];

        $product->setData($args['row']);
        $rule = $args['rule'];
        $storeId = $args['storeId'];
        if ($storeId) {
            $storeIds = [$storeId];
        } else {
            $storeIds = $this->_getWebsitesMap();
        }

        foreach ($storeIds as $storeId) {
            $product->setStoreId($storeId);

            /* Fix for quantity_and_stock_status attribute */
            $isInStock = $product->getData('mfdyc_tmp_is_in_stock');
            $qas = $product->getData('quantity_and_stock_status');

            if (is_array($qas)) {
                if (!$isInStock) {
                    $product->setData('quantity_and_stock_status', [
                        'is_in_stock' => (bool)$isInStock,
                        'qty' => 0,
                    ]);
                } else {
                    $product->setData('quantity_and_stock_status', [
                        'is_in_stock' => (bool)$isInStock,
                        'qty' => isset($qas['qty']) ? $qas['qty'] : 0,
                    ]);
                }
            } else {
                $product->setData('quantity_and_stock_status', $isInStock);
            }
            /* end */

            if ($rule->getConditions()->validate($product)) {
                $this->productIds[] = $product->getId();
            }
        }
    }

    /**
     * Prepare website map
     *
     * @return array
     */
    protected function _getWebsitesMap()
    {
        if ($this->websitesMap === null) {
            $this->websitesMap = [];
            $websites = $this->storeManager->getWebsites();
            foreach ($websites as $website) {
                // Continue if website has no store to be able to create catalog rule for website without store
                if ($website->getDefaultStore() === null) {
                    continue;
                }
                $this->websitesMap[$website->getId()] = $website->getDefaultStore()->getId();
            }
        }

        return $this->websitesMap;
    }
}
