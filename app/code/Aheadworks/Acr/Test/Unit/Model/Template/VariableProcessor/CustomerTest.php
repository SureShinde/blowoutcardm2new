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

use Aheadworks\Acr\Model\Customer\Resolver as CustomerResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Aheadworks\Acr\Model\Template\VariableProcessor\Customer;
use Magento\Customer\Model\Customer as CustomerObject;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class CustomerTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CustomerTest extends TestCase
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
     * @var CustomerFactory
     */
    private $customerFactoryMock;

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
        $this->customerFactoryMock = $this->createMock(CustomerFactory::class);
        $this->customerResolverMock = $this->createMock(CustomerResolver::class);

        $this->customer = $objectManager->getObject(
            Customer::class,
            [
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'customerObject' => $this->customerObjectMock,
                'customerFactory' => $this->customerFactoryMock,
                'customerResolver' => $this->customerResolverMock
            ]
        );
    }

    /**
     * Test Process method
     * @dataProvider data
     */
    public function testProcess($data)
    {
        $customerName = 'test';
        $customerId = $data ? 1 : null;
        $quote = $this->createMock(CartInterface::class);
        $customer = $this->createMock(CustomerObject::class);
        $customer2 = $this->createMock(CustomerObject::class);
        $quote->expects($this->once())
            ->method('getCustomer')
            ->willReturn($customer);
        $customer->expects($this->once())
            ->method('getId')
            ->willReturn($customerId);
        if ($data) {
            $this->customerObjectMock->expects($this->once())
                ->method('load')
                ->with($customerId)
                ->willReturn($customer2);
        } else {
            $this->customerFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($customer2);
            $customer2->expects($this->any())
                ->method('setData')
                ->withAnyParameters()
                ->willReturnSelf();
        }
        $this->assertSame(
            [Variables::CUSTOMER => $customer2],
            $this->customer->process($quote, ['customer_name' => $customerName])
        );
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $customerId = 1;
        $customer = $this->createMock(CustomerObject::class);
        $this->customerResolverMock->expects($this->once())
            ->method('getTestCustomerId')
            ->willReturn($customerId);
        $this->customerObjectMock->expects($this->once())
            ->method('load')
            ->with($customerId)
            ->willReturn($customer);

        $this->assertSame([Variables::CUSTOMER => $customer], $this->customer->processTest([]));
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            [true], [false]
        ];
    }
}
