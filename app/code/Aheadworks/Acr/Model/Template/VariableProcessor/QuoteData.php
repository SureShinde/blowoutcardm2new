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

use Aheadworks\Acr\Model\Quote\Resolver as QuoteResolver;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Aheadworks\Acr\Model\Hydrator\Quote;

/**
 * Class QuoteData
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class QuoteData implements VariableProcessorInterface
{
    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var Quote
     */
    private $hydrator;

    /**
     * @var QuoteResolver
     */
    private $quoteResolver;

    /**
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param QuoteResolver $quoteResolver
     */
    public function __construct(
        QuoteCollectionFactory $quoteCollectionFactory,
        QuoteResolver $quoteResolver,
        Quote $hydrator
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->hydrator = $hydrator;
        $this->quoteResolver = $quoteResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return $this->hydrator->extract($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return $this->hydrator->extract($this->quoteResolver->getTestQuote());
    }
}
