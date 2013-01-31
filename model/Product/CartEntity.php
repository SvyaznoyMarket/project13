<?php

namespace Model\Product;

class CartEntity extends CompactEntity {
    /** @var Service\Entity[] */
    protected $service = [];
    /** @var Warranty\Entity[] */
    protected $warranty = [];
    /** @var Kit\Entity[] */
    protected $kit = [];

    public function __construct(array $data = []) {
        parent::__construct($data);

        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
        if (array_key_exists('warranty', $data) && is_array($data['warranty'])) $this->setWarranty(array_map(function($data) {
            return new Warranty\Entity($data);
        }, $data['warranty']));
        if (array_key_exists('kit', $data) && is_array($data['kit'])) $this->setKit(array_map(function($data) {
            return new Kit\Entity($data);
        }, $data['kit']));
    }

    /**
     * @param Service\Entity[] $services
     */
    public function setService(array $services) {
        $this->service = [];
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
     * @param Warranty\Entity[] $warranties
     * @return void
     */
    public function setWarranty(array $warranties) {
        $this->warranty = [];
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

    /**
     * @param Kit\Entity[] $kits
     */
    public function setKit(array $kits) {
        $this->kit = [];
        foreach ($kits as $kit) {
            $this->addKit($kit);
        }
    }

    /**
     * @param Kit\Entity $kit
     */
    public function addKit(Kit\Entity $kit) {
        $this->kit[] = $kit;
    }

    /**
     * @return Kit\Entity[]
     */
    public function getKit() {
        return $this->kit;
    }
}