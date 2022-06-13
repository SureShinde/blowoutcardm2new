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
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Send
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Send extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::mail_log';

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @param Context $context
     * @param QueueManagementInterface $queueManagement
     */
    public function __construct(
        Context $context,
        QueueManagementInterface $queueManagement
    ) {
        parent::__construct($context);
        $this->queueManagement = $queueManagement;
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
                $result = $this->queueManagement->sendById($queueId);
                if ($result) {
                    $this->messageManager->addSuccessMessage(__('Email was successfully sent.'));
                } else {
                    $this->messageManager->addErrorMessage(__('This email cannot be sent.'));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while sending the email.'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
