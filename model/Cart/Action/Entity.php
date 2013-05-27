<?php

namespace Model\Cart\Action;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $type;
    /** @var string */
    private $number;
    /** @var array */
    private $productIds = [];
    /** @var array */
    private $serviceIds = [];
    /** @var array */
    private $warrantyIds = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('type', $data)) $this->setType($data['type']);
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('product_list', $data) && is_array($data['product_list'])) $this->setProductIds($data['product_list']);
        if (array_key_exists('service_list', $data) && is_array($data['service_list'])) $this->setServiceIds($data['service_list']);
        if (array_key_exists('warranty_list', $data) && is_array($data['warranty_list'])) $this->setWarrantyIds($data['warranty_list']);
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = (string)$type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param array $productIds
     */
    public function setProductIds(array $productIds) {
        $this->productIds = $productIds;
    }

    /**
     * @return array
     */
    public function getProductIds() {
        return $this->productIds;
    }

    /**
     * @param array $serviceIds
     */
    public function setServiceIds(array $serviceIds) {
        $this->serviceIds = $serviceIds;
    }

    /**
     * @return array
     */
    public function getServiceIds() {
        return $this->serviceIds;
    }

    /**
     * @param array $warrantyIds
     */
    public function setWarrantyIds(array $warrantyIds) {
        $this->warrantyIds = $warrantyIds;
    }

    /**
     * @return array
     */
    public function getWarrantyIds() {
        return $this->warrantyIds;
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = (string)$number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }
}
