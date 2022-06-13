<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\UI\DataProvider\Rule\Form;

use Magefan\DynamicCategory\Model\ResourceModel\Rule\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;

/**
 * Class DataProvider
 */
class RuleDataProvider extends AbstractDataProvider
{
    /**
     * @var \Magefan\DynamicCategory\Model\ResourceModel\Rule\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var RequestInterface
     */
    protected $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $ruleCollection,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->request = $request;
        $this->collection = $ruleCollection;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function getMeta()
    {
        if ($currentRuleId = $this->request->getParam('id')) {
            $meta = [
                'what_to_display_product'  => [
                    'children' => [
                        'products_grid' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'rule_id' => $currentRuleId
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            $meta = parent::getMeta();
        }

        return $meta;
    }


    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();

        foreach ($items as $rule) {
            $rule = $rule->load($rule->getId()); //temporary fix
            $data = $rule->getData();

            /* Set data */
            $this->loadedData[$rule->getId()] = $data;
        }
        return $this->loadedData;
    }
}
