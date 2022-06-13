<?php

namespace Meetanshi\AdvanceContact\Plugin;

use Meetanshi\AdvanceContact\Helper\Data;

class Department
{
    protected $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    public function afterExecute(\Magento\Contact\Controller\Index\Post $subject, $result)
    {

        $department = $subject->getRequest()->getParams();

        if ($this->helper->isEnable()) {
            $this->helper->sendMail($department);
        }
        return $result;
    }
}
