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
use Aheadworks\Acr\Api\CouponVariableManagementInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Template\VariableProcessor\Coupon;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Api\Data\StoreInterface;
use Aheadworks\Acr\Api\Data\CouponVariableInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class CouponTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class CouponTest extends TestCase
{
    /**
     * @var Coupon
     */
    private $coupon;

    /**
     * @var CouponVariableManagementInterface
     */
    private $couponVariableManagerMock;

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
        $this->couponVariableManagerMock = $this->createMock(CouponVariableManagementInterface::class);

        $this->coupon = $objectManager->getObject(
            Coupon::class,
            [
                'storeManager' => $this->storeManagerMock,
                'couponVariableManager' => $this->couponVariableManagerMock
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $storeId = 1;
        $ruleId = 1;
        $coupon = $this->createMock(CouponVariableInterface::class);
        $quote = $this->createMock(CartInterface::class);
        $this->couponVariableManagerMock->expects($this->once())
            ->method('getCouponVariable')
            ->with($ruleId, $storeId)
            ->willReturn($coupon);
        $this->assertSame(
            [Variables::COUPON => $coupon],
            $this->coupon->process($quote, ['rule_id' => $ruleId, 'store_id' => $storeId])
        );
    }

    /**
     * Test getTestEmailRecipient method
     */
    public function testProcessTest()
    {
        $coupon = $this->createMock(CouponVariableInterface::class);
        $storeObject = $this->createMock(StoreInterface::class);
        $this->couponVariableManagerMock->expects($this->once())
            ->method('getTestCouponVariable')
            ->willReturn($coupon);
        $this->assertSame([Variables::COUPON => $coupon], $this->coupon->processTest([]));
    }
}
