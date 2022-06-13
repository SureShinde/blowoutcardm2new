<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Controller\Adminhtml\Product\Mui;

/**
 * Class Render
 * @package Magefan\DynamicCategory\Controller\Adminhtml\Product\Mui
 */
class Render extends \Magento\Ui\Controller\Adminhtml\Index\Render
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DynamicCategory::rule';
}
