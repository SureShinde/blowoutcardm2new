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
namespace Aheadworks\Acr\Controller\Adminhtml\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Aheadworks\Acr\Controller\Adminhtml\Rule
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Acr::rules';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param RuleInterfaceFactory $ruleFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        RuleRepositoryInterface $ruleRepository,
        RuleInterfaceFactory $ruleFactory,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->ruleRepository = $ruleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $rule = $this->ruleFactory->create();
        $ruleId = (int)$this->getRequest()->getParam('id');
        if ($ruleId) {
            try {
                /** @var RuleInterface $rule */
                $rule = $this->ruleRepository->get($ruleId);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('Something went wrong while editing the rule.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
        }

        $this->coreRegistry->register('aw_acr_rule', $rule);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Acr::rules')
            ->getConfig()->getTitle()->prepend(
                $ruleId ?  __('Edit Rule') : __('New Rule')
            );
        return $resultPage;
    }
}
