<?php
namespace Imagineer\Address\Block\Customer\Address\Form\Edit;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * 
 */
class Custom extends Template
{
	
	private $address;

	private $addressRepository;

	private $addressFactory;

	private $customerSession;

	private $currentCustomer;


	public function __construct(
		Template\Context $context, 
		AddressRepositoryInterface $addressRepository,
		AddressInterfaceFactory $addressFactory,
		Session $session,
		CurrentCustomer $currentCustomer,
		array $data = [])
	{
		parent::__construct($context, $data);
		$this->addressRepository = $addressRepository;
		$this->addressFactory = $addressFactory;
		$this->customerSession = $session;
		$this->currentCustomer = $currentCustomer;
	}

	protected function _prepareLayout()
	{		
		$addressId = $this->getRequest()->getParam('id');

		if($addressId)
		{
			try{
				$this->address = $this->addressRepository->getById($addressId);	
				if ($this->address->getCustomerId() != $this->customerSession->getCustomerId())
				{
					$this->address = null;
				}
			} catch(NoSuchEntityException $exception){
				$this->address = null;
			}
		}

		if (null === $this->address || !$this->address->getId()) {
			$this->address = $this->addressFactory->create();
		}		

		return parent::_prepareLayout();
	}

	protected function _toHtml()
	{
		$customWidgetBlock = $this->getLayout()->createBlock(
			'Imagineer\Address\Block\Customer\Widget\Custom'
		);

		$customWidgetBlock->setAddress($this->address);

		return $customWidgetBlock->toHtml();
	}

	public function getCustomer()
    {
        return $this->currentCustomer->getCustomer();
    }
}