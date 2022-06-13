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
 * Class Filter
 * @package Aheadworks\Acr\Model\Template
 */
class Filter extends \Magento\Newsletter\Model\Template\Filter
{
    /**
     * For pattern
     */
    const CONSTRUCTION_FOR_PATTERN = '/{{for\s*(.*?)\s*in\s*(.*?)}}(.*?){{\\/for\s*}}/si';

    /**
     * Filter the string as template
     *
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        $this->_modifiers['formatPrice'] = [$this, 'modifierFormatPrice'];
        $this->_modifiers['formatDecimal'] = [$this, 'modifierFormatDecimal'];
        try {
            $value = $this->process($value);
        } catch (\Exception $e) {
            // Since a single instance of this class can be used to filter content multiple times, reset callbacks to
            // prevent callbacks running for unrelated content (e.g., email subject and email body)
            $this->resetAfterFilterCallbacks();

            if ($this->_appState->getMode() == \Magento\Framework\App\State::MODE_DEVELOPER) {
                $value = sprintf(__('Error filtering template: %s'), $e->getMessage());
            } else {
                $value = __("We're sorry, an error has occurred while generating this email.");
            }
            $this->_logger->critical($e);
        }
        return $value;
    }

    /**
     * Process string
     *
     * @param string $value
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function process($value)
    {
        // "depend", "if", "template" and "for" directives should be first
        foreach ([
                     self::CONSTRUCTION_DEPEND_PATTERN => 'dependDirective',
                     self::CONSTRUCTION_IF_PATTERN => 'ifDirective',
                     self::CONSTRUCTION_TEMPLATE_PATTERN => 'templateDirective',
                     self::CONSTRUCTION_FOR_PATTERN => 'forDirective',
                 ] as $pattern => $directive) {
            if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                foreach ($constructions as $construction) {
                    $callback = [$this, $directive];
                    if (!is_callable($callback)) {
                        continue;
                    }
                    try {
                        $replacedValue = call_user_func($callback, $construction);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    $value = str_replace($construction[0], $replacedValue, $value);
                }
            }
        }

        if (preg_match_all(self::CONSTRUCTION_PATTERN, $value, $constructions, PREG_SET_ORDER)) {
            foreach ($constructions as $construction) {
                $callback = [$this, $construction[1] . 'Directive'];
                if (!is_callable($callback)) {
                    continue;
                }
                try {
                    $replacedValue = call_user_func($callback, $construction);
                } catch (\Exception $e) {
                    throw $e;
                }
                $value = str_replace($construction[0], $replacedValue, $value);
            }
        }

        $value = $this->afterFilter($value);
        return $value;
    }

    /**
     * forDirective
     *
     * @param string[] $construction
     * @return string
     */
    public function forDirective($construction)
    {
        $content = '';
        if (count($this->templateVars) == 0) {
            $content = $construction[0];
        }
        $iterated = $this->getVariable($construction[2], []);
        if (is_array($iterated) || $iterated instanceof \IteratorAggregate) {
            foreach ($iterated as $variable) {
                $this->templateVars[$construction[1]] = $variable;
                $content .= $this->process($construction[3]);
            }
        }
        return $content;
    }

    /**
     * Generate widget with emulation frontend area
     *
     * @param string[] $construction
     * @return string
     * @throws \Exception
     */
    public function widgetDirective($construction)
    {
        if (isset($construction[2])) {
            $construction[2] .= sprintf(' store_id ="%s"', $this->getStoreId());
        }
        return $this->_appState->emulateAreaCode('frontend', [$this, 'generateWidget'], [$construction]);
    }

    /**
     * Generate widget
     *
     * @param string[] $construction
     * @return string
     */
    public function generateWidget($construction)
    {
        $params = $this->getParameters($construction[2]);
        $params['area'] = 'frontend';

        // Determine what name block should have in layout
        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
        }
        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preConfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preConfigured['widget_type'];
            $params = $preConfigured['parameters'];
        } else {
            return '';
        }
        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc.
        $xml = $this->_widget->getWidgetByClassType($type);
        if ($xml === null) {
            return '';
        }
        // define widget block and check the type is instance of Widget Interface
        $widget = $this->_layoutFactory->create()->createBlock($type, $name, ['data' => $params]);
        if ($params['type'] == 'Aheadworks\Autorelated\Block\Widget\Related') {
            $widget->setTemplate('Aheadworks_Acr::email/arp.phtml');
        }
        if (!$widget instanceof \Magento\Widget\Block\BlockInterface) {
            return '';
        }
        return $widget->toHtml();
    }

    /**
     * modifierFormatPrice
     *
     * @param string $value
     * @return float
     */
    public function modifierFormatPrice($value)
    {
        if (isset($this->templateVars['store'])) {
            $value = $this->templateVars['store']->getCurrentCurrency()->format($value);
        }
        return $value;
    }

    /**
     * modifierFormatDecimal
     *
     * @param string $value
     * @return string
     */
    public function modifierFormatDecimal($value)
    {
        if (is_numeric($value)) {
            $params = func_get_args();
            array_shift($params);
            if (!count($params)) {
                $value = number_format($value);
            } elseif (count($params) == 1) {
                $value = number_format($value, $params[0]);
            } elseif (count($params) == 3) {
                $value = number_format($value, $params[0], $params[1], $params[2]);
            }
        }
        return $value;
    }
}
