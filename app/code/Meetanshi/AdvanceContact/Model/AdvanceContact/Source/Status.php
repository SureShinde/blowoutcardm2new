<?php
namespace Meetanshi\AdvanceContact\Model\AdvanceContact\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Meetanshi\AdvanceContact\Model\AdvanceContact;

class Status implements OptionSourceInterface
{
    protected $popup;

    public function __construct(AdvanceContact $popup)
    {
        $this->popup = $popup;
    }
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public static function getOptionArray()
    {
        return [1 => __('Enabled'), 0 => __('Disabled')];
    }
}
