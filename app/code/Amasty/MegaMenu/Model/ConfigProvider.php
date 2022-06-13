<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Model;

use Amasty\MegaMenuLite\Model\ConfigProvider as ConfigProviderAliasLite;

class ConfigProvider extends ConfigProviderAliasLite
{
    const STICKY_ENABLED = 'general/sticky';

    const SHOW_ICONS = 'general/show_icons';

    const MOBILE_TEMPLATE = 'general/mobile_template';

    public function isStickyEnabled(?int $storeId = null): ?bool
    {
        return (bool) $this->isSetFlag(self::STICKY_ENABLED, $storeId);
    }

    public function getIconsStatus(): string
    {
        return (string) $this->getValue(self::SHOW_ICONS);
    }

    public function getMobileTemplateClass(?int $storeId = null): ?string
    {
        return (string) $this->getValue(self::MOBILE_TEMPLATE, $storeId);
    }
}
