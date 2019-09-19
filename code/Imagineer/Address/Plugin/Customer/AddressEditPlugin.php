<?php
namespace Imagineer\Address\Plugin\Customer;

use Magento\Framework\View\LayoutInterface;


/**
 * 
 */
class AddressEditPlugin
{
		
	private $layout;

	public function __construct(
		LayoutInterface $layout
	)
	{
		$this->layout = $layout;
	}

	public function afterGetNameBlockHtml(
		\Magento\Customer\Block\Address\Edit $edit,
		$result
	)
	{
		$customBlock = $this->layout->createBlock(
				'Imagineer\Address\Block\Customer\Address\Form\Edit\Custom',
				'imagineer_extra_checkout_address_fields'
			);
		return $result . $customBlock->toHtml();
	}
}