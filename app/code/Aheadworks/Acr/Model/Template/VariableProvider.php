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
namespace Aheadworks\Acr\Model\Template;

use Aheadworks\Acr\Api\Data\QueueInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Acr\Api\CartHistoryRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class VariableProvider
 * @package Aheadworks\Acr\Model\Template
 */
class VariableProvider
{
    /**
     * @var CartHistoryRepositoryInterface
     */
    private $cartHistoryRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var VariableProcessor
     */
    private $variableProcessor;

    /**
     * @param CartHistoryRepositoryInterface $cartHistoryRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartHistoryRepositoryInterface $cartHistoryRepository,
        CartRepositoryInterface $cartRepository,
        VariableProcessor $variableProcessor
    ) {
        $this->cartHistoryRepository = $cartHistoryRepository;
        $this->cartRepository = $cartRepository;
        $this->variableProcessor = $variableProcessor;
    }

    /**
     * Get template variables data
     *
     * @param QueueInterface $queueItem
     * @return array
     */
    public function getTemplateVarsData(QueueInterface $queueItem)
    {
        try {
            $historyItem = $this->cartHistoryRepository->get($queueItem->getCartHistoryId());
            $emailData = json_decode($historyItem->getCartData(), true);
            try {
                $quote = $this->cartRepository->get($emailData['entity_id']);
                $emailVariables = $this->variableProcessor->processTemplateVariable(
                    $quote,
                    [
                        'store_id' => $queueItem->getStoreId(),
                        'rule_id' => $queueItem->getRuleId(),
                        'customer_name' => $emailData['customer_name'],
                        'variables' => [
                            Variables::QUOTE,
                            Variables::CUSTOMER,
                            Variables::STORE,
                            Variables::CART_RESTORE_LINK,
                            Variables::COUPON
                        ]
                    ]
                );
                $emailData = array_merge($emailData, $emailVariables);
            } catch (NoSuchEntityException $e) {
                throw new LocalizedException(__("Event object is missing"));
            }
        } catch (NoSuchEntityException $e) {
            $emailData = [];
        }
        return $emailData;
    }

    /**
     * Get test template variables data
     *
     * @param int $storeId
     * @return array
     */
    public function getTestTemplateVarsData($storeId)
    {
        return $this->variableProcessor->processTemplateVariableTest(
            [
                'store_id' => $storeId,
                'variables' => [
                    Variables::QUOTE,
                    Variables::CUSTOMER,
                    Variables::STORE,
                    Variables::COUPON,
                    Variables::QUOTE_DATA,
                    Variables::CUSTOMER_DATA,
                    Variables::CART_RESTORE_LINK
                ]
            ]
        );
    }
}
