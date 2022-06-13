<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Apply
 */
class Apply extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magefan_DynamicCategory::rule';

    /**
     * @var \Magefan\DynamicCategory\Model\DynamicCategoryAction
     */
    protected $dynamicCategoryAction;

    /**
     * @var
     */
    protected $logger;

    /**
     * @var \Magefan\DynamicCategory\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollection;

    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * Apply constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction
     * @param \Magefan\DynamicCategory\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction,
        \Magefan\DynamicCategory\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magefan\DynamicCategory\Model\Config $config
    ) {
        $this->config = $config;
        $this->ruleCollection = $ruleCollectionFactory;
        $this->dynamicCategoryAction = $dynamicCategoryAction;
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
            $countRules = $this->ruleCollection->create()->getSize();

            if (!$countRules) {
                $this->messageManager->addError(__('Cannot find any rule.'));
            }

            if ($this->config->isEnabled()) {
                $this->dynamicCategoryAction->execute();
                $this->messageManager->addSuccess(__('Rules has been applied.'));
            } else {
                $this->messageManager->addNotice(__('Please enable the extension to apply rules.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong. %1', $e->getMessage()));
        }

        return $resultRedirect;
    }
}
