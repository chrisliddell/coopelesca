<?php
namespace Imagineer\AddressSetup\Model\Eav\Entity\Attribute\Source;
use Imagineer\Directory\Model\ResourceModel\District\Collection;
class District extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
   /**
     *
     * @var \Imagineer\Directory\Model\ResourceModel\District\Collection
     */
    protected $_collection;

    private $_logger;

    /**
     *
     * @param \Imagineer\Directory\Model\ResourceModel\County\Collection $collection
     */
    public function __construct(\Imagineer\Directory\Model\ResourceModel\District\Collection $collection, \Psr\Log\LoggerInterface $logger)
    {
        $this->_collection = $collection;
        $this->_logger = $logger;
    }

    /**
     *
     * {@inheritDoc}
     * @see \Magento\Eav\Model\Entity\Attribute\Source\SourceInterface::getAllOptions()
     */
    public function getAllOptions()
   {
       $ret = $this->_collection->toOptionIdArray();
       $this->_logger->info(print_r($ret, true));

       return $ret;
    }

}

