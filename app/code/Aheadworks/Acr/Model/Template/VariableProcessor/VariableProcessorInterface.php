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

/**
 * Interface VariableProcessorInterface
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
interface VariableProcessorInterface
{
    /**
     * @param QuoteData $quote
     * @param array $params
     * @return array
     */
    public function process($quote, $params);

    /**
     * @param array $params
     * @return array
     */
    public function processTest($params);
}
