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

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Customer as CustomerObject;
use Aheadworks\Acr\Model\Source\Email\Variables;
use Aheadworks\Acr\Model\Customer\Resolver as CustomerResolver;

/**
 * Class Customer
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Customer implements VariableProcessorInterface
{
    const DEFAULT_CUSTOMER_NAME = 'Guest';

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var CustomerObject
     */
    private $customerObject;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerResolver
     */
    private $customerResolver;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param CustomerObject $customerObject
     * @param CustomerResolver $customerResolver
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        CustomerObject $customerObject,
        CustomerResolver $customerResolver,
        CustomerFactory $customerFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerObject = $customerObject;
        $this->customerResolver = $customerResolver;
        $this->customerFactory = $customerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        $customerId = $quote->getCustomer()->getId();
        if ($customerId) {
            $customer = $this->customerObject->load($customerId);
        } else {
            $customer = $this->customerFactory->create();
            $customer->setData('name', $params['customer_name']);
            $customer->setData('firstname', $params['customer_name']);
            $customer->setData('lastname', $params['customer_name']);
        }
        return [Variables::CUSTOMER => $customer];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::CUSTOMER => $this->customerObject->load($this->customerResolver->getTestCustomerId())];
    }
}
