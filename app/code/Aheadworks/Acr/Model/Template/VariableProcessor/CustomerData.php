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
namespace Aheadworks\Acr\Model\Template\VariableProcessor;

use Aheadworks\Acr\Model\Customer\Resolver as CustomerResolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class CustomerData
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class CustomerData implements VariableProcessorInterface
{
    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CustomerResolver
     */
    private $customerResolver;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param ObjectManagerInterface $objectManager
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CustomerResolver $customerResolver
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        ObjectManagerInterface $objectManager,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CustomerResolver $customerResolver
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->objectManager = $objectManager;
        $this->customerRepository = $customerRepositoryInterface;
        $this->customerResolver = $customerResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        if ($quote->getCustomer()->getId()) {
            $customer = $this->customerRepository->getById($quote->getCustomer()->getId());
            $customerData = [
                'email'  => $customer->getEmail(),
                'store_id'  => $customer->getStoreId(),
                'customer_group_id'  => $customer->getGroupId(),
                'customer_firstname' => $customer->getFirstname(),
                'customer_lastname' => $customer->getLastname(),
            ];
        } else {
            $customerData['customer_firstname'] = $params['customer_name'];
            $customerData['customer_lastname'] = $params['customer_name'];
            $customerData['customer_name'] = $params['customer_name'];
        }
        return $customerData;
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        $customerId = $this->customerResolver->getTestCustomerId();
        $customerData = [];
        if ($customerId) {
            $customer =$this->customerRepository->getById($customerId);
            $customerData = [
                'email'  => $customer->getEmail(),
                'store_id'  => $customer->getStoreId(),
                'customer_group_id'  => $customer->getGroupId(),
                'customer_firstname' => $customer->getFirstname(),
                'customer_lastname' => $customer->getLastname(),
                'customer_name' => $customer->getFirstname() .' '. $customer->getLastname('lastname'),
            ];
        }

        return $customerData;
    }
}
