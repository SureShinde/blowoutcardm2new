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

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Api\QueueRepositoryInterface;
use Aheadworks\Acr\Api\QueueManagementInterface;
use Magento\Backend\App\Action\Context;
use Aheadworks\Acr\Model\Preview\Storage;

/**
 * Class Preview
 * @package Aheadworks\Acr\Controller\Adminhtml\Queue
 */
class Preview extends \Magento\Backend\App\Action
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
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @param Context $context
     * @param QueueRepositoryInterface $queueRepository
     * @param QueueManagementInterface $queueManagement
     * @param Storage $storage
     */
    public function __construct(
        Context $context,
        QueueRepositoryInterface $queueRepository,
        QueueManagementInterface $queueManagement,
        Storage $storage
    ) {
        parent::__construct($context);
        $this->queueRepository = $queueRepository;
        $this->queueManagement = $queueManagement;
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $queueId = (int)$this->getRequest()->getParam('id');
        if ($queueId) {
            try {
                /** @var QueueInterface $queue */
                $queue = $this->queueRepository->get($queueId);

                $preview = $this->queueManagement->getPreview($queue);
                $this->storage->savePreviewData($preview);

                $this->_view->loadLayout(['aw_acr_preview'], true, true, false);
                $this->_view->renderLayout();
                return;
            } catch (\Exception $e) {
                // do nothing
            }
        }
        $this->_forward('noroute');
    }
}
