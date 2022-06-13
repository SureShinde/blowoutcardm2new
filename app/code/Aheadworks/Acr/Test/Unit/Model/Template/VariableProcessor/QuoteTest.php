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
use Aheadworks\Acr\Model\Template\VariableProcessor\Quote;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\Quote as QuoteModel;
use Aheadworks\Acr\Model\Source\Email\Variables;
use Aheadworks\Acr\Model\Quote\Resolver as QuoteResolver;

/**
 * Class QuoteTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class QuoteTest extends TestCase
{
    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactoryMock;

    /**
     * @var QuoteResolver
     */
    private $quoteResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->quoteCollectionFactoryMock = $this->createMock(QuoteCollectionFactory::class);
        $this->quoteResolverMock = $this->createMock(QuoteResolver::class);

        $this->quote = $objectManager->getObject(
            Quote::class,
            [
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
                'quoteResolver' => $this->quoteResolverMock
            ]
        );
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $quote = $this->createMock(QuoteModel::class);
        $this->quoteResolverMock->expects($this->once())
            ->method('getTestQuote')
            ->willReturn($quote);

        $this->assertSame([Variables::QUOTE => $quote], $this->quote->processTest([]));
    }
}
