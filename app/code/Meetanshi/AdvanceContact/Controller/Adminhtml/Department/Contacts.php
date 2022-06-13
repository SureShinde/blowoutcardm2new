<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml\Department;

use Meetanshi\AdvanceContact\Controller\Adminhtml\Department;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Contacts extends Department
{

    protected $resultPageFactory = false;

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
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Contact Inquiries')));
        return $resultPage;
    }
}
