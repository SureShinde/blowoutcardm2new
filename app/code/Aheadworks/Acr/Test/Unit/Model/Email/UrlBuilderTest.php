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
namespace Aheadworks\Acr\Test\Unit\Model\Email;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Email\UrlBuilder;
use Aheadworks\Acr\Model\Email\UrlInterface as UrlInterfaceFront;
use Magento\Backend\Model\UrlInterface as UrlInterfaceBack;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UrlBuilderTest
 * @package Aheadworks\Acr\Test\Unit\Model\Email
 */
class UrlBuilderTest extends TestCase
{
    /**
     * @var UrlBuilder
     */
    private $url;

    /**
     * @var UrlInterface
     */
    private $front;

    /**
     * @var UrlInterface
     */
    private $back;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->front = $this->createMock(UrlInterfaceFront::class);
        $this->back = $this->createMock(UrlInterfaceBack::class);

        $this->url = $objectManager->getObject(
            UrlBuilder::class,
            [
                'urlBuilders' => [
                    'frontend' => $this->front,
                    'adminhtml' => $this->back
                ]
            ]
        );
    }

    /**
     * Test Process method
     * @dataProvider data
     */
    public function testGetUrl($bool, $areaCode)
    {
        $href = $bool ? 'http://123.com/path/to/model' : null;
        $path = 'path/to/model';
        $params = [];
        if ($areaCode == 'frontend') {
            $urlBuilder = $this->front;
        } elseif ($areaCode == 'adminhtml') {
            $urlBuilder = $this->back;
        }
        $scope = $this->createMock(StoreManagerInterface::class);
        if ($bool) {
            $urlBuilder->expects($this->once())
                ->method('setScope')
                ->willReturnSelf();
            $urlBuilder->expects($this->once())
                ->method('getUrl')
                ->with($path, $params)
                ->willReturn($href);
        }

        $this->assertSame($href, $this->url->getUrl($path, $scope, $params, $areaCode));
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            [true, 'frontend'],
            [false, 'frontend'],
            [true, 'adminhtml'],
            [false, 'adminhtml']
        ];
    }
}
