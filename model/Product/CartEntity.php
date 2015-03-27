<?php

namespace Model\Product;

class CartEntity extends CompactEntity {
    /** @var Service\Entity[] */
    protected $service = [];
    /** @var Kit\Entity[] */
    protected $kit = [];

    public function __construct(array $data = []) {
        parent::__construct($data);

        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
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