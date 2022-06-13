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
namespace Aheadworks\Acr\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\Acr\Model\ThirdPartyModule
 */
class Manager
{
    /**
     * Magento page builder module name
     */
    const PAGE_BUILDER_MODULE_NAME = 'Magento_PageBuilder';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Magento page builder module enabled
     *
     * @return bool
     */
    public function isMagentoPageBuilderModuleEnabled()
    {
        return $this->moduleList->has(self::PAGE_BUILDER_MODULE_NAME);
    }
}
