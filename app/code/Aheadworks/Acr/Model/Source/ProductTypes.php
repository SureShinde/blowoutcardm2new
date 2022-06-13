<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Acr
 * @version    1.1.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Acr\Model\Source;

use Magento\Catalog\Api\ProductTypeListInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Convert\DataObject as ConvertDataObject;

/**
 * Class Groups
 * @package Aheadworks\Acr\Model\Source
 */
class ProductTypes implements OptionSourceInterface
{
    /**
     * @var ProductTypeListInterface
     */
    private $productTypeList;

    /**
     * @var ConvertDataObject
     */
    private $objectConverter;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ProductTypeListInterface $productTypeList
     * @param ConvertDataObject $objectConverter
     */
    public function __construct(
        ProductTypeListInterface $productTypeList,
        ConvertDataObject $objectConverter
    ) {
        $this->productTypeList = $productTypeList;
        $this->objectConverter = $objectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $productTypesOptions = $this->objectConverter->toOptionArray(
                $this->productTypeList->getProductTypes(),
                'name',
                'label'
            );
            array_unshift($productTypesOptions, [
                'value' => 'all',
                'label' => __('All Product Types')
            ]);
            foreach ($productTypesOptions as $key => $option) {
                if ($option['value'] == 'grouped') {
                    unset($productTypesOptions[$key]);
                }
            }
            $this->options = $productTypesOptions;
        }

        return $this->options;
    }
}
