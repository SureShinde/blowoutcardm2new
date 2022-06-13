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
namespace Aheadworks\Acr\Model\Template;

/**
 * Class VariableProcessor
 *
 * @package Aheadworks\Acr\Model\Template
 */
class VariableProcessor
{
    /**
     * @var array
     */
    private $processors;

    /**
     * @param array $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Process template variable
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $params
     * @return array
     */
    public function processTemplateVariable($quote, $params)
    {
        return $this->processVariable($quote, $params, false);
    }

    /**
     * Process template variableTest
     *
     * @param array $params
     * @return array
     */
    public function processTemplateVariableTest($params)
    {
        return $this->processVariable(null, $params, true);
    }

    /**
     * Process variable
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $params
     * @param bool $forTest
     * @return array
     */
    private function processVariable($quote, $params, $forTest)
    {
        $variables = isset($params['variables']) ? $params['variables'] : [];
        $variableValues = [];
        foreach ($variables as $variable) {
            if (isset($this->processors[$variable])) {
                $variableValue = $forTest
                    ? $this->processors[$variable]->processTest($params)
                    : $this->processors[$variable]->process($quote, $params);
            }
            $variableValues = array_merge($variableValues, $variableValue);
        }
        return $variableValues;
    }
}
