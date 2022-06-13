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
use Aheadworks\Acr\Model\Template\VariableProcessor\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class StoreTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class StoreTest extends TestCase
{
    /**
     * @var Store
     */
    private $store;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->store = $objectManager->getObject(
            Store::class,
            [
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $storeId = 1;
        $quote = $this->createMock(CartInterface::class);
        $store = $this->createMock(StoreManagerInterface::class);

        $quote->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->assertSame([Variables::STORE => $store], $this->store->process($quote, []));
    }

    /**
     * Test testProcess method
     */
    public function testProcessTest()
    {
        $storeId = 1;
        $store = $this->createMock(StoreManagerInterface::class);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($store);

        $this->assertSame([Variables::STORE => $store], $this->store->processTest(['store_id' => $storeId]));
    }
}
