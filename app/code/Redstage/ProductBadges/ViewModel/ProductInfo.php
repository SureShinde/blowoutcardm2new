<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Product description block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Redstage\ProductBadges\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Redstage\ProductBadges\Helper\Newlabel;

/**
 * Attributes attributes block
 *
 * @api
 * @since 100.0.2
 */
class ProductInfo implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

     /**
     * @var Helper
     */
    protected $_newLabel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        array $data = [],
        Newlabel $newLabel
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_coreRegistry = $registry;
        $this->_newLabel = $newLabel;
        //parent::__construct($context, $data);
    }

    /**
     * Returns a Product
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }
        return $this->_product;
    }

     /**
     * $excludeAttr is optional array of attribute codes to exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkSpecialPrice(array $excludeAttr = [])
    {
        $product = $this->getProduct();
        $specialPriceSp = (float)$product->getData('special_price');
        if($specialPriceSp){
            return 1;
        }else{
            return 0;
        }
        
    }

    public function checkNewProduct()
    {
        $product = $this->getProduct();
        $newproduct = $this->_newLabel->isProductNew($product);
        
        return $newproduct;
    }

     /**
     * $excludeAttr is optional array of attribute codes to exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkPreOrder()
    {
        $product = $this->getProduct();        
        $date_now = date("Y-m-d");;
        $releasedate    = date('Y-m-d', strtotime($product->getData('releasedate')));

        if ($date_now > $releasedate) {
            return 0;
        }else{
            return 1;
        }        
    }

    public function getReleasedate(){
        $product = $this->getProduct();
        return date('m/d/y', strtotime($product->getData('releasedate')));
    }
    
}
