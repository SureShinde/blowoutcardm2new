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

use Aheadworks\Acr\Model\Quote\Resolver as QuoteResolver;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use Aheadworks\Acr\Model\Template\VariableProcessor\QuoteData;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Quote\Model\Quote;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Acr\Model\Hydrator\Quote as HydratorQuote;

/**
 * Class QuoteDataTest
 * @package Aheadworks\Acr\Test\Unit\Model\Template\VariableProcessor
 */
class QuoteDataTest extends TestCase
{
    /**
     * @var QuoteData
     */
    private $quoteData;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactoryMock;

    /**
     * @var DataObjectProcessor
     */
    private $hydratorMock;

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
        $this->hydratorMock = $this->createMock(HydratorQuote::class);
        $this->quoteResolverMock = $this->createMock(QuoteResolver::class);

        $this->quoteData = $objectManager->getObject(
            QuoteData::class,
            [
                'quoteCollectionFactory' => $this->quoteCollectionFactoryMock,
                'hydrator' => $this->hydratorMock,
                'quoteResolver' => $this->quoteResolverMock
            ]
        );
    }

    /**
     * Test Process method
     */
    public function testProcess()
    {
        $quoteData = [
            'test1' => 'test',
            'test2' => 'test',
            'test3' => 'test'
        ];
        $quote = $this->createMock(Quote::class);
        $this->hydratorMock->expects($this->once())
            ->method('extract')
            ->with($quote)
            ->willReturn($quoteData);
        $this->assertSame($quoteData, $this->quoteData->process($quote, []));
    }

    /**
     * Test ProcessTest method
     */
    public function testProcessTest()
    {
        $quoteData = [
            'test1' => 'test',
            'test2' => 'test',
            'test3' => 'test'
        ];
        $quote = $this->createMock(Quote::class);
        $this->quoteResolverMock->expects($this->once())
            ->method('getTestQuote')
            ->willReturn($quote);
        $this->hydratorMock->expects($this->once())
            ->method('extract')
            ->with($quote)
            ->willReturn($quoteData);

        $this->assertSame($quoteData, $this->quoteData->processTest([]));
    }
}
