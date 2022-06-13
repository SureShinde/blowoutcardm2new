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

use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Aheadworks\Acr\Model\Source\Email\Variables;
use Aheadworks\Acr\Model\Quote\Resolver as QuoteResolver;

/**
 * Class Quote
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Quote implements VariableProcessorInterface
{
    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

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
        QuoteResolver $quoteResolver
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteResolver = $quoteResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::QUOTE => $quote];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::QUOTE => $this->quoteResolver->getTestQuote()];
    }
}
