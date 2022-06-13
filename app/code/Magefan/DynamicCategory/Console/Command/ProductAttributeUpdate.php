<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\DynamicCategory\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProductAttributeUpdate
 */
class ProductAttributeUpdate extends Command
{
    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * @var \Magefan\DynamicCategory\Model\UpdateProductAttributes
     */
    protected $updateProductAttributes;

    /**
     * ProductAttributeUpdateCron constructor.
     * @param \Magefan\DynamicCategory\Model\Config $config
     * @param \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes
     * @param null $name
     */
    public function __construct(
        \Magefan\DynamicCategory\Model\Config $config,
        \Magefan\DynamicCategory\Model\UpdateProductAttributes $updateProductAttributes,
        $name = null
    ) {
        $this->config = $config;
        $this->updateProductAttributes = $updateProductAttributes;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        if ($this->config->isEnabled()) {
            $this->updateProductAttributes->update();
            $output->writeln("Magefan Attributes have been applied.");
        } else {
            $output->writeln("Dynamic category extension is disabled. Please turn on it.");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("magefan:dyc:product-attribute:update");
        $this->setDescription("Update Dynamic Product Attributes");
        parent::configure();
    }
}
