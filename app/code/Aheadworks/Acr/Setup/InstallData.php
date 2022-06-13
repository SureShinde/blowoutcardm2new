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
namespace Aheadworks\Acr\Setup;

use Aheadworks\Acr\Api\Data\RuleInterface;
use Aheadworks\Acr\Api\Data\RuleInterfaceFactory;
use Aheadworks\Acr\Api\RuleRepositoryInterface;
use Aheadworks\Acr\Model\Source\Rule\Status as RuleStatus;
use Aheadworks\Acr\Model\Sample;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class InstallData
 * @package Aheadworks\Acr\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var RuleInterfaceFactory
     */
    private $ruleFactory;

    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var Sample
     */
    private $sampleData;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param RuleInterfaceFactory $ruleFactory
     * @param RuleRepositoryInterface $ruleRepository
     * @param Sample $sampleData
     * @param Json $serializer
     */
    public function __construct(
        RuleInterfaceFactory $ruleFactory,
        RuleRepositoryInterface $ruleRepository,
        Sample $sampleData,
        Json $serializer
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleRepository = $ruleRepository;
        $this->sampleData = $sampleData;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        foreach ($this->sampleData->get() as $data) {
            try {
                /** @var RuleInterface|AbstractModel $rule */
                $rule = $this->ruleFactory->create();
                $rule
                    ->setData($data)
                    ->setStatus(RuleStatus::DISABLED)
                    ->setStoreIds([0])
                    ->setProductTypeIds(['all'])
                    ->setCustomerGroups(['all'])
                    ->setProductConditions($this->serializer->serialize([]))
                    ->setCartConditions($this->serializer->serialize([]));
                $this->ruleRepository->save($rule);
            } catch (\Exception $e) {
                // do nothing
            }
        }

        $setup->endSetup();
    }
}
