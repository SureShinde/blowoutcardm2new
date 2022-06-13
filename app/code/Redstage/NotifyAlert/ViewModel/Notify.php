<?php
/**
 * Created By : Rohan Hapani
 */
namespace Redstage\NotifyAlert\ViewModel;
// use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Context;
class Notify implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;
    /**
     * @param \Magento\Catalog\Model\CustomerFactory     $customerFactory
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        $this->_httpContext = $httpContext;
    }

    public function isCustomerLoggedIn(){
        return (bool)$this->_httpContext->getValue(Context::CONTEXT_AUTH);
    }
}