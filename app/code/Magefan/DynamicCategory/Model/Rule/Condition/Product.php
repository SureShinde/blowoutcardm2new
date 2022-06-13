<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Product\AbstractProduct;

/**
 * Class Product
 */
class Product extends \Magento\CatalogRule\Model\Rule\Condition\Product
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $attrCode = $this->getAttribute();
        if ('mfdc_created_in_days' == $attrCode) {
            $this->createdInDaysToCreatedAtDate();
            $attrCode = $this->getAttribute();
        }
        $oldAttrValue = $model->getData($attrCode);
        if ($oldAttrValue === null) {
            if ($this->getOperator() === '<=>') {
                return true;
            }
            return false;
        }

        $this->_setAttributeValue($model);
        $result = $this->validateAttribute($model->getData($attrCode));

        return (bool)$result;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [];
        $this->_addSpecialAttributes($attributes);
        asort($attributes);
        $this->setAttributeOption($attributes);
        return $this;
    }

    /**
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['mfdc_reviews_count'] = __('Reviews rating');
        $attributes['mfdc_reviews_score'] = __('Score rating');
        $attributes['mfdc_created_in_days'] = __('Created (in days)');
        $attributes['mfdc_is_on_sale'] = __('Is on sale');
        $attributes['mfdc_is_new'] = __('Is New');
        $attributes['mfdc_stock_qty'] = __('Quantity');
        $attributes['mfdc_best_sellers_per_week'] = __('Best Sellers (QTY) Per Week');
        $attributes['mfdc_best_sellers_per_month'] = __('Best Sellers (QTY) Per Month');
        $attributes['mfdc_best_sellers_per_three_months'] = __('Best Sellers (QTY) Per Three Months');
        $attributes['mfdc_best_sellers_per_year'] = __('Best Sellers (QTY) Per Year');
    }

    /**
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['string'] = ['==', '!=', '>=', '<=', '>', '<'];
        }
        return $this->_defaultOperatorInputByType;
    }

    public function createdInDaysToCreatedAtDate()
    {
        $dateTime = $this->getDataTime();
        $days = (int)$this->getValue();

        $this->setData('attribute', 'created_at');
        $this->setData('value', date('Y-m-d 00:00:00', strtotime($dateTime->gmtDate('Y/m/d')) - $days * 86400));
        switch ($this->getOperator()) {
            case '==':
                break;
            case '>=':
                $this->setData('operator', '<=');
                break;
            case '<=':
                $this->setData('operator', '>=');
                break;
            case '>':
                $this->setData('operator', '<');
                break;
            case '<':
                $this->setData('operator', '>');
                break;
            default:
                $this->setData('operator', '==');
                break;
        }
        return $this;
    }

    private function getDataTime()
    {
        if (null === $this->dateTime) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->dateTime = $objectManager->create(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        }

        return  $this->dateTime;
    }
}
