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
 * Class DynamicCategory
 */
class DynamicCategory extends Command
{
    /**
     * @var \Magefan\DynamicCategory\Model\Config
     */
    protected $config;

    /**
     * @var \Magefan\DynamicCategory\Model\DynamicCategoryAction
     */
    protected $dynamicCategoryAction;

    /**
     * DynamicCategory constructor.
     * @param \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction
     * @param \Magefan\DynamicCategory\Model\Config $config
     * @param null $name
     */
    public function __construct(
        \Magefan\DynamicCategory\Model\DynamicCategoryAction $dynamicCategoryAction,
        \Magefan\DynamicCategory\Model\Config $config,
        $name = null
    ) {
        $this->config = $config;
        $this->dynamicCategoryAction = $dynamicCategoryAction;
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
            $this->dynamicCategoryAction->execute();
            $output->writeln("Dynamic category rules have been applied.");
        } else {
            $output->writeln("Dynamic category extension is disabled. Please turn on it.");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("magefan:dyc:apply-rules");
        $this->setDescription("Apply Dynamic Category Rules");

        parent::configure();
    }
}
