<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Controller\Adminhtml;

/**
 * Class Rule
 */
class Rule extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'dynamiccategory_rule_form_data';
    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Magefan_DynamicCategory::rule';
    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Magefan\DynamicCategory\Model\Rule';
    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Magefan_DynamicCategory::rule';
    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
