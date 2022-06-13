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
namespace Aheadworks\Acr\Ui\Component\Listing\Columns\Rule;

use Aheadworks\Acr\Api\Data\RuleInterface;

/**
 * Class When
 * @package Aheadworks\Acr\Ui\Component\Listing\Columns\Rule
 * @codeCoverageIgnore
 */
class When extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            if ($item[RuleInterface::EMAIL_SEND_DAYS] > 0) {
                $days = sprintf(
                    "%s %s ",
                    $item[RuleInterface::EMAIL_SEND_DAYS],
                    $item[RuleInterface::EMAIL_SEND_DAYS] > 1 ? __('days') : __('day')
                );
            } else {
                $days = '';
            }
            if ($item[RuleInterface::EMAIL_SEND_HOURS] > 0) {
                $hours = sprintf(
                    "%s %s ",
                    $item[RuleInterface::EMAIL_SEND_HOURS],
                    $item[RuleInterface::EMAIL_SEND_HOURS] > 1 ? __('hours') : __('hour')
                );
            } else {
                $hours = '';
            }
            if ($item[RuleInterface::EMAIL_SEND_MINUTES] > 0) {
                $minutes = sprintf(
                    "%s %s ",
                    $item[RuleInterface::EMAIL_SEND_MINUTES],
                    __('minutes')
                );
            } else {
                $minutes = '';
            }
            $result = $days . $hours . $minutes;

            if ($result == '') {
                $result = __('Immediately');
            } else {
                $result .= __('later');
            }

            $item['when'] = $result;
        }

        return $dataSource;
    }
}
