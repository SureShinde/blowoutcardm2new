<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class UpdateProductAttributes
 * @package Magefan\DynamicCategory\Controller\Adminhtml\Rule
 */
class UpdateProductAttributes extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DynamicCategory::rule';

    /**
     * @var \Magefan\DynamicCategory\Model\UpdateProductAttributes
     */
    protected $updateProductAttributes;

    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * UpdateProductAttributes constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magefan\DynamicCategory\Model\Config $config
     * @param \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magefan\DynamicCategory\Model\Config $config,
        \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes
    ) {
        $this->updateProductAttributes = $updateProductAttributes;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Action execute
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        try {
            if (!$this->config->isEnabled()) {
                $this->messageManager->addError(__('The extension is disabled'));
            } else {
                $this->updateProductAttributes->update();
                $this->messageManager->addSuccess(__('Attributes have been updated.'));
            }

        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong. %1', $e->getMessage()));
        }

        return $resultRedirect;
    }
}
