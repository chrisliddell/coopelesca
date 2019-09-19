<?php
namespace Imagineer\Address\Block\Customer\Widget;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

/**
 * 
 */
class Custom extends Template
{

	private $addressMetadata;
    protected $logger;
    protected $countyCollection;
    protected $districtCollection;
    protected $townCollection;

	public function __construct(
		Template\Context $context,
		AddressMetadataInterface $addressMetadata,
        \Psr\Log\LoggerInterface $logger,
        \Imagineer\Directory\Model\ResourceModel\County\Collection $countyCollection,
        \Imagineer\Directory\Model\ResourceModel\District\Collection $districtCollection,
        \Imagineer\Directory\Model\ResourceModel\Town\Collection $townCollection,
		array $data = []
	)
	{		
		parent::__construct($context, $data);
		$this->addressMetadata = $addressMetadata;
        $this->logger = $logger;
        $this->countyCollection = $countyCollection;
        $this->districtCollection = $districtCollection;
        $this->townCollection = $townCollection;
	}

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('widget/custom.phtml');
	}

	public function isRequired($attributeCode)
	{
		return $this->getAttribute($attributeCode) 
			? $this->getAttribute($attributeCode)->isRequired()
			: false;
	}

	public function getFieldId($attributeId)
	{
		return $attributeId;
	}

	public function getFieldLabel($attributeCode)
	{
		return $this->getAttribute($attributeCode)
			? $this->getAttribute($attributeCode)->getFrontendLabel()
			: __('Custom');
	}

	public function getValue($attributeCode)
	{
		$address = $this->getAddress();		
		if($address instanceof AddressInterface){
			return $address->getCustomAttribute($attributeCode) 
				? $address->getCustomAttribute($attributeCode)->getValue()
				: null;
		}
		return null;
	}

	public function getOptions($attributeCode)
	{
		$address = $this->getAddress();
		if($address instanceof AddressInterface){
                    $this->logger->info('OPTIONS: ' . print_r($this->getAttribute($attributeCode)->getOptions(), true));
            return $this->getAttribute($attributeCode)->getOptions();
		}
		return [];
	}

	public function isNullRegion()
	{
		$address = $this->getAddress();
		$this->logger->info("isNullRegion");
		if($address instanceof AddressInterface){
			$this->logger->info("isNullRegion2");
			if(is_null($address->getRegion())){
				$this->logger->info("issetRegion: TRUE");
				return true;
			}
		}
		$this->logger->info("issetRegion: TRUE");
		return false;
	}

	public function filterCounties()
	{
		$arrRes = [];

		$address = $this->getAddress();
		if($address instanceof AddressInterface){

			$regionId = $address->getRegion()->getRegionId();

			$this->logger->info("regionId: " . $regionId);

			$arrCounties = $this->countyCollection->addRegionFilter(
	            (string)$regionId
	        )->toOptionIdArray();
	       
	       	if (!empty($arrCounties)) {
	            foreach ($arrCounties as $county) {
	                $arrRes[] = $county;
	            }
	        }
	        $this->logger->info(print_r($arrRes[0]['label'], true));		        
	        return $arrRes;
		}
		return $arrRes;
	}

	public function filterDistricts()
	{
		$arrRes = [];

		$address = $this->getAddress();
		if($address instanceof AddressInterface){

			$countyId = $this->getValue('county');

			$this->logger->info("countyId: " . $countyId);

			$arrDistricts = $this->districtCollection->addCountyFilter(
	            (string)$countyId
	        )->toOptionIdArray();
	       
	       	if (!empty($arrDistricts)) {
	            foreach ($arrDistricts as $district) {
	                $arrRes[] = $district;
	            }
	        }
	        $this->logger->info(print_r($arrRes[1]['label'], true));		        
	        return $arrRes;
		}
		return $arrRes;
	}

	public function filterTowns()
	{
		$arrRes = [];

		$address = $this->getAddress();
		if($address instanceof AddressInterface){

			$districtId = $this->getValue('district');

			$this->logger->info("districtId abc: " . $districtId);

			$arrTowns = $this->townCollection->addDistrictFilter(
	            $districtId
	        )->toOptionIdArray();
	       	$this->logger->info('$arrTowns: ' . print_r($arrTowns, true));
	       	if (!empty($arrTowns)) {
	            foreach ($arrTowns as $town) {
	                $arrRes[] = $town;
	            }
	        }
	        $this->logger->info(print_r($arrRes[0]['label'], true));		        
	        return $arrRes;
		}
		return $arrRes;
	}

	private function getAttribute($attributeCode)
	{
		try{
			$attribute = $this->addressMetadata->getAttributeMetadata($attributeCode);
		} catch (NoSuchEntityException $exception){
			return null;			
		}
		return $attribute;
	}
}