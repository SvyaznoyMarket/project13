<?php

namespace Model\Order\Delivery;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $price = 0;
    /** @var int */
    private $typeId;
    /** @var \DateTime */
    private $deliveredAt;

    public function __construct(array $data = []) {
        if (array_key_exists('delivery_type_id', $data)) $this->setId($data['delivery_type_id']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('delivery_id', $data)) $this->setTypeId($data['delivery_id']);
        if (array_key_exists('delivery_date', $data) && $data['delivery_date'] && ('0000-00-00' != $data['delivery_date'])) {
            try {
                $this->setDeliveredAt(new \DateTime($data['delivery_date']));
            } catch(\Exception $e) {
                \App::logger()->error($e);
            }
        }
    }

    /**
     * @param \DateTime $deliveredAt
     */
    public function setDeliveredAt(\DateTime $deliveredAt = null) {
        $this->deliveredAt = $deliveredAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveredAt() {
        return $this->deliveredAt;
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
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = $typeId ? (int)$typeId : null;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }
}