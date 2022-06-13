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
namespace Aheadworks\Acr\Model\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Resolver
 * @package Aheadworks\Acr\Model\Customer
 */
class Resolver
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve test customer id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTestCustomerId()
    {
        $customer = null;
        $searchCriteria = $this->searchCriteriaBuilder
            ->setPageSize(1)
            ->create();
        $searchResult = $this->customerRepository->getList($searchCriteria)->getItems();

        if (count($searchResult)) {
            $customer = array_shift($searchResult);
        } else {
            throw new LocalizedException(__("We couldn't find any customer"));
        }

        return $customer->getId();
    }
}
