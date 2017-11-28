<?php
namespace Bakeway\CommissionLog\Block\View;
class View extends \Magento\Framework\View\Element\Template
{
    /*
     *\Bakeway\CommissionLog\Model\CommissionLogFactory
     */
    protected $commissionLogCollection;

    /*
     * \Magento\Customer\Model\Session
     */
    protected $customerSesssion;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bakeway\CommissionLog\Model\CommissionLogFactory $commissionLogFactory,
        \Magento\Customer\Model\Session $customerSesssion,
        array $data = []
    )
    {
        $this->commissionLogCollection = $commissionLogFactory;
        $this->customerSesssion = $customerSesssion;
        parent::__construct($context, $data);

    }

    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Commission'));
        return parent::_prepareLayout();
    }

    public function getCommissionList()
    {
        $_Collection = $this->commissionLogCollection->create()->getCollection()
            ->setOrder('created_at', 'DESC')
            ->addFieldToFilter('seller_id', $this->getCustomerId());
        return $_Collection;
    }

    public function getCustomerId()
    {
        return $this->customerSesssion->getCustomerId();
    }
}
