<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magefan\DynamicCategory\Model\UpdateProductAttributes;
use Magefan\DynamicCategory\Model\Config;

/**
 * Class ProductSave
 * @deprecated
 */
class ProductSave implements ObserverInterface
{

    /**
     * @var UpdateProductAttributes
     */
    protected $updateProductAttributes;

    /**
     * @var Config
     */
    protected $config;

    /**
     * ProductSave constructor.
     * @param Config $config
     * @param UpdateProductAttributes $updateProductAttributes
     */
    public function __construct(
        Config $config,
        UpdateProductAttributes $updateProductAttributes
    ) {
        $this->config = $config;
        $this->updateProductAttributes =  $updateProductAttributes;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Zend_Db_Statement_Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return; /* Do not execute this observer */

        $product = $observer->getProduct();
        if (!$product || !$product->getId()) {
            return;
        }
        if (!$this->config->isEnabled()) {
            return;
        }

        $this->updateProductAttributes->update($product->getId());
    }
}
