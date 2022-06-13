<?php

namespace Redstage\ReleaseCalendar\Block\Product;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

class ReleaseProductList extends ListProduct
{
    public function getLoadedProductCollection()
    {
        return $this->_productCollection;
    }

    public function setProductCollection(AbstractCollection $collection)
    {
        $this->_productCollection = $collection;
    }

    public function getMode()
    {
    	return "list";
    }
}