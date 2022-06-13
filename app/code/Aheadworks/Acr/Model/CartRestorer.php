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
namespace Aheadworks\Acr\Model;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Acr\Api\Data\CartRestoreInterface;
use Aheadworks\Acr\Api\CartRestoreRepositoryInterface;
use Aheadworks\Acr\Api\CartHistoryManagementInterface;
use Aheadworks\Acr\Model\Cart\Merger;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CartRestorer
 * @package Aheadworks\Acr\Model
 */
class CartRestorer
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Merger
     */
    private $merger;

    /**
     * @var CartRestoreRepositoryInterface
     */
    private $cartRestoreRepository;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CartHistoryManagementInterface
     */
    private $cartHistoryManagement;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CartRestoreRepositoryInterface $cartRestoreRepository
     * @param CheckoutSession $checkoutSession
     * @param Merger $merger
     * @param CartHistoryManagementInterface $cartHistoryManagement
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CartRestoreRepositoryInterface $cartRestoreRepository,
        CheckoutSession $checkoutSession,
        Merger $merger,
        CartHistoryManagementInterface $cartHistoryManagement
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cartRestoreRepository = $cartRestoreRepository;
        $this->checkoutSession = $checkoutSession;
        $this->merger = $merger;
        $this->cartHistoryManagement = $cartHistoryManagement;
    }

    /**
     * Restore cart
     *
     * @param $restoreCode
     * @param $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function restore($restoreCode, $customerId)
    {
        $cartRestoreItem = $this->cartRestoreRepository->getByCode($restoreCode);
        $cartId = $this->cartHistoryManagement->getCartIdByEventHistoryId($cartRestoreItem->getEventHistoryId());
        try {
            $currentQuote = $this->checkoutSession->getQuote();
            $this->merger->mergeCartById($cartId, $currentQuote, $customerId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        return true;
    }
}
