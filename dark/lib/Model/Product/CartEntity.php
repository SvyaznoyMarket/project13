<?php

namespace Model\Product;

class CartEntity extends CompactEntity {
    /** @var Service\Entity[] */
    protected $service = array();

    public function __construct(array $data = array()) {
        parent::__construct($data);

        if (array_key_exists('service', $data) && is_array($data['service'])) $this->setService(array_map(function($data) {
            return new Service\Entity($data);
        }, $data['service']));
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
}