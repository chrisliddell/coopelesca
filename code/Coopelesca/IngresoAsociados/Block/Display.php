<?php
namespace Coopelesca\IngresoAsociados\Block;

class Display extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}

	public function saludo()
	{
		return __('Esto es un mensaje');
	}
}
