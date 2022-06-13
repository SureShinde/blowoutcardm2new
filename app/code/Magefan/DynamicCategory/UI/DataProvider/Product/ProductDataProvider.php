<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\UI\DataProvider\Product;

/**
 * Class ProductDataProvider
 * @package Magefan\DynamicCategory\UI\DataProvider\Product
 */
class ProductDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    const PRODUCTS_KEY = 'dynamiccategory_conditions_applied';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    protected $storeRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->storeRepository = $storeRepository;
        $this->collection = $collectionFactory->create();
        $this->registry = $registry;
    }

    /**
     * @param array $productIds
     */
    public function updateCollection($productIds)
    {
        /** @var \Magento\Catalog\Ui\DataProvider\Product\ProductCollection $collection */
        $collection = parent::getCollection();
        $collection->addIdFilter($productIds);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $appliedProducts = $this->registry->registry(self::PRODUCTS_KEY);
        $appliedProducts = $this->prepareData($appliedProducts);
        $collection = $this->getCollection();
        $collection->joinField('qty', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
        $collection->addFieldToFilter('entity_id', ['in' => array_keys($appliedProducts)]);

        if (!$collection->isLoaded()) {
            $collection->load();
        }
        $items = $collection->toArray();
        if ($appliedProducts) {
            foreach ($items as &$item) {
                if (isset($appliedProducts[$item['entity_id']])) {
                    $stores = $appliedProducts[$item['entity_id']];
                    if (in_array('0', $stores)) {
                        $item['stores'] = ['0'];
                    } else {
                        $item['stores'] = $stores;
                    }
                }
            }
        }

        return [
            'totalRecords' => $collection->getSize(),
            'items' => array_values($items),
        ];
    }


    /**
     * @param $ids
     * @return array
     */
    protected function prepareData($ids)
    {
        $resultIds = [];
        if ($ids) {
            $stores = $this->storeRepository->getList();
            /** @var $productCollection Collection */
            $productCollection = $this->getCollection();
            foreach ($stores as $store) {
                $storeId = $store->getId();
                if (!empty($storeId)) {
                    $productCollection->setStoreId($storeId)->addIdFilter($ids);
                    foreach ($ids as $id) {
                        $resultIds[$id][] = $store->getId();
                    }
                }
            }

        }

        return $resultIds;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return [
            'mfdynamiccategory_product_listing_data_source' => [
                'arguments' => [
                    'data' => [
                        'js_config' => [
                            'rule_id' => $this->getRequest()->getParam('rule_id')
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    private function getRequest()
    {
        return $this->data['config']['request'];
    }
}
