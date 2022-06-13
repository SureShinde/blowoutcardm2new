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
namespace Aheadworks\Acr\Model\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class Resolver
 * @package Aheadworks\Acr\Model\Customer
 */
class Resolver
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve test quote
     *
     * @return CartInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTestQuote()
    {
        $quote = null;
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->setPageSize(1)
            ->create();
        $searchResult = $this->quoteRepository->getList($searchCriteria)->getItems();

        if (count($searchResult)) {
            $quote = array_shift($searchResult);
        } else {
            throw new LocalizedException(__("We couldn't find any quote"));
        }

        return $quote;
    }
}
