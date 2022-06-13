<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Cron;

/**
 * Update Dinamic Product Attributes
 */
class ProductAttributeUpdateCron
{
    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * @var UpdateProductAttributes
     */
    protected $updateProductAttributes;

    /**
     * ProductReviewAttributeUpdate constructor.
     * @param \Magefan\DynamicCategory\Model\Config $config
     * @param \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes
     */
    public function __construct(
        \Magefan\DynamicCategory\Model\Config $config,
        \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes
    ) {
        $this->config = $config;
        $this->updateProductAttributes = $updateProductAttributes;
    }

    /**
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }
        $this->updateProductAttributes->update();
    }
}
