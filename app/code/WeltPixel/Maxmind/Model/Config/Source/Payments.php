<?php
namespace WeltPixel\Maxmind\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Payments
 * @package WeltPixel\Maxmin\Model\Config\Source
 */
class Payments implements ArrayInterface
{

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $paymentsHelper;

    /**
     * Payments constructor.
     * @param \Magento\Payment\Helper\Data $paymentsHelper
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentsHelper
    ) {
        $this->paymentsHelper = $paymentsHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $methods = [];
        $paymentMethods = $this->paymentsHelper->getPaymentMethods();
        foreach ($paymentMethods as $code => $data) {
            $paymentGroup = (isset($data['group'])) ? '(' . ucfirst($data['group']) . ') ' : '';
            $paymentTitle = (isset($data['title'])) ? $paymentGroup . $data['title'] : '';
            if ($paymentTitle) {
                $methods[$code] = $paymentTitle;
            }
        }
        asort($methods);

        $options = [];
        foreach ($methods as $code => $paymentTitle) {
            $options[] = [
                'value' => $code,
                'label' => $paymentTitle . ' - (' . $code . ')'
            ];
        }

        return $options;
    }
}
