<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml\Department;

use Meetanshi\AdvanceContact\Model\AdvanceContactFactory;
use Magento\Backend\App\Action\Context;
use Meetanshi\AdvanceContact\Controller\Adminhtml\Department;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Delete extends Department
{
    protected $department;
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        AdvanceContactFactory $advanceContactFactory,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->department = $advanceContactFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($id) {
            try {
                $model = $this->department->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Department deleted successfully.'));
                $resultRedirect->setUrl('*/*/index');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $resultRedirect->setUrl('*/*/edit', ['id' => $id]);
                return $resultRedirect;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a department to delete.'));
        $resultRedirect->setUrl('*/*/index');
        return $resultRedirect;
    }
}
