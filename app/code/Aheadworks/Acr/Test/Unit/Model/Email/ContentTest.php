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

use Aheadworks\Acr\Model\Config;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Acr\Model\Email\Content;
use Aheadworks\Acr\Model\Email\Template\Content as TemplateContent;

/**
 * Class ContentTest
 * @package Aheadworks\Acr\Test\Unit\Model\Email
 */
class ContentTest extends TestCase
{
    /**
     * @var TemplateContent
     */
    private $templateContentMock;

    /**
     * @var Config
     */
    private $configMock;

    /**
     * @var Content
     */
    private $content;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->templateContentMock = $this->createMock(TemplateContent::class);
        $this->content = $objectManager->getObject(
            Content::class,
            [
                'config' => $this->configMock,
                'templateContent' => $this->templateContentMock,
            ]
        );
    }

    /**
     * Test getFullContent method
     *
     * @param string $expected
     */
    public function testGetFullContent()
    {
        $storeId = '1';
        $content = 'content';
        $headerTemplateContent = 'header';
        $footerTemplateContent = 'footer';
        $headerTemplateId = '1';
        $footerTemplateId = '2';
        $this->configMock->expects($this->once())
            ->method('getHeaderTemplateId')
            ->with($storeId)
            ->willReturn($headerTemplateId);
        $this->configMock->expects($this->once())
            ->method('getFooterTemplateId')
            ->with($storeId)
            ->willReturn($footerTemplateId);

        $this->templateContentMock->expects($this->exactly(2))
            ->method('getTemplateContent')
            ->willReturnMap([
                [$headerTemplateId, $storeId, $headerTemplateContent],
                [$footerTemplateId, $storeId, $footerTemplateContent]
            ]);

        $result = $headerTemplateContent . $content . $footerTemplateContent;

        $this->assertSame($result, $this->content->getFullContent($content, $storeId));
    }
}
