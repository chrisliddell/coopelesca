<?php 
namespace Imagineer\CustomPayment\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface; 
 
class PaymentResponse extends Template implements BlockInterface {

	protected $_template = "widget/payment-response.phtml";

}
