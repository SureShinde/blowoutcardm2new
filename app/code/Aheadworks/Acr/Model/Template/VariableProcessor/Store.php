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

use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Acr\Model\Source\Email\Variables;

/**
 * Class Store
 *
 * @package Aheadworks\Acr\Model\Template\VariableProcessor
 */
class Store implements VariableProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process($quote, $params)
    {
        return [Variables::STORE => $this->storeManager->getStore($quote->getStoreId())];
    }

    /**
     * {@inheritdoc}
     */
    public function processTest($params)
    {
        return [Variables::STORE => $this->storeManager->getStore($params['store_id'])];
    }
}
