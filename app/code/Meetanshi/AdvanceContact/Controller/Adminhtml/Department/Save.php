<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml\Department;

use Meetanshi\AdvanceContact\Controller\Adminhtml\Department;
use Meetanshi\AdvanceContact\Model\AdvanceContactFactory;
use Magento\Backend\Model\Session;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends Department
{

    protected $filter;
    protected $collectionFactory;
    protected $department;
    protected $session;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AdvanceContactFactory $department,
        Session $session
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->department = $department;
        $this->session = $session;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        try {

            if ($data) {

                $model = $this->department->create();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                }

                $model->setData('name', $data['name']);
                $model->setData('email', $data['email']);
                $model->setData('is_active', $data['is_active']);
                $model->save();

                $this->messageManager->addSuccessMessage(__('Department saved successfully.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/index');
                return;

            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->_getSession()->setFormData($data);
        $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        return;
    }
}
