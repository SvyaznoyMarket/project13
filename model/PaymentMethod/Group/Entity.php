<?php

namespace Model\PaymentMethod\Group;

use Model\PaymentMethod\Entity as PaymentMethod;

class Entity {

    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $description;
    /** @var PaymentMethod[] */
    private $paymentMethods;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('payment_methods', $data) && !empty($data['payment_methods'])) {
            $methods = [];
            foreach ($data['payment_methods'] as $methodData) {
                $methods[] = new PaymentMethod($methodData);
            }

            $this->setPaymentMethods($methods);
        }
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
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
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param PaymentMethod[] $paymentMethods
     */
    public function setPaymentMethods(array $paymentMethods) {
        $this->paymentMethods = $paymentMethods;
    }

    /**
     * @return PaymentMethod[]
     */
    public function getPaymentMethods() {
        return $this->paymentMethods;
    }
} 