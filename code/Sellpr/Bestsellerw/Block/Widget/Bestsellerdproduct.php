<?php
namespace Sellpr\Bestsellerw\Block\Widget;

class Bestsellerdproduct extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	protected $_template = 'widget/bestsellerdproduct.phtml';
	 
    const DEFAULT_PRODUCTS_COUNT = 10;
     const DEFAULT_IMAGE_WIDTH = 240;
     const DEFAULT_IMAGE_HEIGHT = 300;
     protected $_productsCount;
    protected $httpContext;
    protected $_resourceFactory;
    protected $_catalogProductVisibility;
    protected $_productCollectionFactory;
    protected $_imageHelper;
    protected $_cartHelper;
   public function __construct(
    \Magento\Catalog\Block\Product\Context $context,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \Magento\Reports\Model\Grouped\CollectionFactory $collectionFactory,
        \Magento\Reports\Helper\Data $reportsData,
        array $data = []
    ) {
        $this->_resourceFactory = $resourceFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_reportsData = $reportsData;
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        parent::__construct($context, $data);
    }
 
 public function imageHelperObj(){
        return $this->_imageHelper;
    }
    /**
     get featured product collection
     */
   public function getBestsellerProduct(){
     $limit = $this->getProductLimit();   
     $resourceCollection = $this->_resourceFactory->create('Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection');
     $resourceCollection->setPageSize($limit);
     return $resourceCollection;
   }
   
    /**
      Get the configured limit of products
     */
    public function getProductLimit() {
		if($this->getData('productcount')==''){
			return DEFAULT_PRODUCTS_COUNT;
		}
        return $this->getData('productcount');
    }
  public function getProductimagewidth() {
		if($this->getData('imagewidth')==''){
			return DEFAULT_IMAGE_WIDTH;
		}
        return $this->getData('imagewidth');
    }
     
    public function getProductimageheight() {
		if($this->getData('imageheight')==''){
			return DEFAULT_IMAGE_HEIGHT;
		}
        return $this->getData('imageheight');
    }
     public function getAddToCartUrl($product, $additional = [])
    {
			return $this->_cartHelper->getAddUrl($product, $additional);
    }
     
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['zone'] = isset($arguments['zone'])
            ? $arguments['zone']
            : $renderZone;
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
