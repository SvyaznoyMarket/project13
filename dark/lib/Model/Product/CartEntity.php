<?php

namespace Model\Product;

class CartEntity extends CompactEntity {
    /** @var Service\Entity[] */
    protected $service = array();
    /** @var Warranty\Entity[] */
    protected $warranty = array();

    public function __construct(array $data = array()) {
        parent::__construct($data);

        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
        if (array_key_exists('warranty', $data) && is_array($data['warranty'])) $this->setWarranty(array_map(function($data) {
            return new Warranty\Entity($data);
        }, $data['warranty']));
    }

    /**
     * @param Service\Entity[] $services
     */
    public function setService(array $services) {
        $this->service = array();
        foreach ($services as $service) {
            $this->addService($service);
        }
    }

    /**
     * @param Service\Entity $service
     */
    public function addService(Service\Entity $service) {
        $this->service[] = $service;
    }

    /**
     * @return Service\Entity[]
     */
    public function getService() {
        return $this->service;
    }

    /**
     * @param Warranty\Entity[] $services
     */
    public function setWarranty(array $warranties) {
        $this->warranty = array();
        foreach ($warranties as $warranty) {
            $this->addWarranty($warranty);
        }
    }

    /**
     * @param Warranty\Entity $warranty
     */
    public function addWarranty(Warranty\Entity $warranty) {
        $this->warranty[] = $warranty;
    }

    /**
     * @return Warranty\Entity[]
     */
    public function getWarranty() {
        return $this->warranty;
    }

    /**
     * @param int $warrantyId
     * @return Warranty\Entity|null
     */
    public function getWarrantyById($warrantyId) {
        $return = null;
        foreach ($this->warranty as $warranty) {
            if ($warrantyId == $warranty->getId()) {
                $return = $warranty;
                break;
            }
        }

        return $return;
    }
}