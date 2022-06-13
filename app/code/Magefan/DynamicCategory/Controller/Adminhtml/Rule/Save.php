<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Controller\Adminhtml\Rule;

use Magefan\DynamicCategory\Controller\Adminhtml\Rule;

/**
 * Class Save
 */
class Save extends Rule
{
    /**
     * After action
     * @return void
     */
    protected function _afterAction()
    {
        if ($this->getRequest()->getParam('auto_apply')) {
            $this->_redirect('*/*/apply');
        }
    }
}
