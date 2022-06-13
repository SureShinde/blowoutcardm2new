<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Cron;

/**
 * Apply Dynamic Category rules
 */
class DynamicCategory
{
    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * @var \Magefan\DynamicCategory\Model\DynamicCategoryAction
     */
    protected $dynamicCategoryAction;

    /**
     * DynamicCategory constructor.
     * @param \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction
     * @param \Magefan\DynamicCategory\Model\Config $config
     */
    public function __construct(
        \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction,
        \Magefan\DynamicCategory\Model\Config $config
    ) {
        $this->config = $config;
        $this->dynamicCategoryAction = $dynamicCategoryAction;
    }

    public function execute()
    {
        if ($this->config->isEnabled()) {
            $this->dynamicCategoryAction->execute();
        }
    }
}
