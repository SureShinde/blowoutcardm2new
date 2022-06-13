<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Acr
 * @version    1.1.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Acr\Controller\Adminhtml\Queue;

use Aheadworks\Acr\Api\QueueManagementInterface;
use Aheadworks\Acr\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassCancel
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class MassCancel extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var QueueCollectionFactory
     */
    private $queueCollectionFactory;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        Filter $filter,
        QueueCollectionFactory $queueCollectionFactory,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->queueManagement = $queueManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->queueCollectionFactory->create());
            $count = 0;
            foreach ($collection->getAllIds() as $queueId) {
                $result = $this->queueManagement->cancelById($queueId);
                if ($result) {
                    $count++;
                }
            }
            if ($count > 0) {
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 email(s) have been cancelled.', $count)
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('None of selected emails can be cancelled.')
                );
            }
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('Something went wrong while cancelling the email(s).')
            );
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
