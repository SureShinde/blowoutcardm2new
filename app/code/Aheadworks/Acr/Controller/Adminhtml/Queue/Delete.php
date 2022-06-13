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

use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;

    /**
     * @param Context $context
     * @param QueueRepositoryInterface $queueRepository
     */
    public function __construct(
        Context $context,
        QueueRepositoryInterface $queueRepository
    ) {
        parent::__construct($context);
        $this->queueRepository = $queueRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $queueId = (int)$this->getRequest()->getParam('id');
        if ($queueId) {
            try {
                $this->queueRepository->deleteById($queueId);
                $this->messageManager->addSuccessMessage(__('Email was successfully deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the email.'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
