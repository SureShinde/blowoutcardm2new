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

/**
 * Class Name
 * @package Aheadworks\Acr\Ui\Component\Listing\Columns\Rule
 * @codeCoverageIgnore
 */
class Name extends \Magento\Ui\Component\Listing\Columns\Column
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
            $item['name'] = $this->getLink($item['id'], $item['name']);
        }

        return $dataSource;
    }

    /**
     * Get link for name
     *
     * @param int $entityId
     * @param string $name
     * @return string
     */
    private function getLink($entityId, $name)
    {
        $url = $this->context->getUrl('aw_acr/rule/edit', ['id' => $entityId]);
        return '<a href="' . $url . '">' . htmlspecialchars($name) . '</a>';
    }
}
