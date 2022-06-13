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
namespace Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Aheadworks\Acr\Model\Template\VariableProcessor\CustomerData;
use Magento\Customer\Model\Customer as CustomerObject;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Customer\Resolver as CustomerResolver;

/**
 * Class CustomerDataTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CustomerDataTest extends TestCase
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactoryMock;

    /**
     * @var ObjectManagerInterface
     */
    private $customerObjectMock;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterfaceMock;

    /**
     * @var CustomerResolver
     */
    private $customerResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->customerCollectionFactoryMock = $this->createMock(CustomerCollectionFactory::class);
        $this->customerObjectMock = $this->createMock(CustomerObject::class);
        $this->customerRepositoryInterfaceMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerResolverMock = $this->createMock(CustomerResolver::class);

        $this->customer = $objectManager->getObject(
            CustomerData::class,
            [
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'customerObject' => $this->customerObjectMock,
                'customerRepository' => $this->customerRepositoryInterfaceMock,
                'customerResolver' => $this->customerResolverMock
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $customerId = 1;
        $email  = 'email@test.com';
        $storeId  = 1;
        $customerGroupId  = 1;
        $customerFirstname  = 'test';
        $customerLastname  = 'test';
        $quote = $this->createMock(CartInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $quote->expects($this->exactly(2))
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerId);
        $this->customerRepositoryInterfaceMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $customer->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $customer->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $customer->expects($this->once())
            ->method('getFirstname')
            ->willReturn($customerFirstname);
        $customer->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastname);

        $customerData = [
            'email'  => $email,
            'store_id'  => $storeId,
            'customer_group_id'  => $customerGroupId,
            'customer_firstname' => $customerFirstname,
            'customer_lastname' => $customerLastname,
        ];
        $this->assertSame($customerData, $this->customer->process($quote, []));
    }

    /**
     * Test testProcess method
     */
    public function testProcessTest()
    {
        $customerId = 1;
        $email  = 'email@test.com';
        $storeId  = 1;
        $customerGroupId  = 1;
        $customerFirstname  = 'test';
        $customerLastname  = 'test';
        $customerName  = 'test test';
        $customer = $this->createMock(CustomerInterface::class);

        $this->customerResolverMock->expects($this->once())
            ->method('getTestCustomerId')
            ->willReturn($customerId);
        $this->customerRepositoryInterfaceMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $customer->expects($this->once())
            ->method('getEmail')
            ->willReturn($email);
        $customer->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $customer->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);
        $customer->expects($this->exactly(2))
            ->method('getFirstname')
            ->willReturn($customerFirstname);
        $customer->expects($this->exactly(2))
            ->method('getLastname')
            ->willReturn($customerLastname);

        $customerData = [
            'email'  => $email,
            'store_id'  => $storeId,
            'customer_group_id'  => $customerGroupId,
            'customer_firstname' => $customerFirstname,
            'customer_lastname' => $customerLastname,
            'customer_name' => $customerName,
        ];
        $this->assertSame($customerData, $this->customer->processTest([]));
    }
}
