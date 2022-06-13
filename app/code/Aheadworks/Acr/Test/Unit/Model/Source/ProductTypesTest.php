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
namespace Aheadworks\Acr\Test\Unit\Model\Source;

use Magento\Catalog\Api\Data\ProductTypeInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Source\ProductTypes;
use Magento\Catalog\Api\ProductTypeListInterface;
use Magento\Framework\Convert\DataObject as ConvertDataObject;

/**
 * Class ProductTypesTest
 * Test for \Aheadworks\Acr\Model\Source\ProductTypes
 *
 * @package Aheadworks\Acr\Test\Unit\Model\Source
 */
class ProductTypesTest extends TestCase
{
    /**
     * @var ProductTypes
     */
    private $model;

    /**
     * @var ProductTypeListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productTypeListMock;

    /**
     * @var ConvertDataObject|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->productTypeListMock = $this->getMockForAbstractClass(ProductTypeListInterface::class);
        $this->objectConverterMock = $this->createPartialMock(ConvertDataObject::class, ['toOptionArray']);

        $this->model = $objectManager->getObject(
            ProductTypes::class,
            [
                'productTypeList' => $this->productTypeListMock,
                'objectConverter' => $this->objectConverterMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $productTypeMock = $this->getMockForAbstractClass(ProductTypeInterface::class);
        $this->productTypeListMock->expects($this->once())
            ->method('getProductTypes')
            ->willReturn([$productTypeMock]);
        $this->objectConverterMock->expects($this->once())
            ->method('toOptionArray')
            ->with([$productTypeMock], 'name', 'label')
            ->willReturn([]);

        $this->assertTrue(is_array($this->model->toOptionArray()));
    }
}
