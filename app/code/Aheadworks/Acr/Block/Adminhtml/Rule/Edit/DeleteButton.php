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
namespace Aheadworks\Acr\Block\Adminhtml\Rule\Edit;

use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Aheadworks\Acr\Block\Adminhtml\Rule\Edit
 */
class DeleteButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        $ruleId = $this->request->getParam('id');
        if ($ruleId && $this->ruleRepository->get($ruleId)) {
            $confirmMessage = __('Are you sure you want to do this?');
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => sprintf(
                    "deleteConfirm('%s', '%s')",
                    $confirmMessage,
                    $this->urlBuilder->getUrl('*/*/delete', ['id' => $ruleId])
                ),
                'sort_order' => 20
            ];
        }
        return $data;
    }
}
