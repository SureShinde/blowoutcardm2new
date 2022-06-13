<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml\Department;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\AdvanceContact\Controller\Adminhtml\Department;

class Edit extends Department
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Department'));
        return $resultPage;
    }
}
