<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class Department extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Meetanshi_AdvanceContact::meetanshi_advancecontact')
            ->_addBreadcrumb(__('Contact Department'), __('Contact Department'));
        return $this;
    }

    protected function _isAllowed()
    {
        return true;
    }
}
