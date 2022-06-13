<?php

namespace Meetanshi\AdvanceContact\Controller\Adminhtml\Contacts;

use Meetanshi\AdvanceContact\Controller\Adminhtml\Department;
use Magento\Ui\Component\MassAction\Filter;
use Meetanshi\AdvanceContact\Model\ResourceModel\ContactList\CollectionFactory;
use Meetanshi\AdvanceContact\Model\ContactListFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massdelete extends Department
{

    protected $filter;
    protected $resultPageFactory;
    protected $collectionFactory;
    protected $contactList;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        ContactListFactory $contactList,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->contactList = $contactList;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productsUpdated = 0;
            foreach ($collection as $item) {
                $contactListItem = $this->contactList->create()->load($item['id']);
                $contactListItem->delete();
                $productsUpdated++;
            }
            if ($productsUpdated) {
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $productsUpdated));
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
