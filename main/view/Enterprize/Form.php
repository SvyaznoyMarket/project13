<?php

namespace View\Enterprize;

class Form {
    /** @var string */
    private $name;
    /** @var string */
    private $email;
    /** @var string */
    private $phone;
    /** @var string */
    private $enterprizeCoupon;
    /** @var bool */
    private $agree;
    /** @var array */
    private $errors = array(
        'global'            => null,
        'name'              => null,
        'email'             => null,
        'phone'             => null,
        'enterprize_coupon' => null,
        'agree'             => null,
    );

    public function __construct(array $data = []) {
        $this->fromArray($data);
    }

    /**
     * @param array $data
     */
    public function fromArray(array $data) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('email', $data)) $this->setEmail($data['email']);
        if (array_key_exists('phone', $data)) $this->setPhone($data['phone']);
        if (array_key_exists('enterprize_coupon', $data)) $this->setEnterprizeCoupon($data['enterprize_coupon']);
        if (array_key_exists('agree', $data)) $this->setAgree($data['agree']);
    }

    /**
     * @param \Model\User\Entity $entity
     */
    public function fromEntity(\Model\User\Entity $entity) {
        $this->setName($entity->getName());
        $this->setEmail($entity->getEmail());
        $this->setPhone($entity->getMobilePhone());
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
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = (string)$email;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone) {
        $this->phone = (string)$phone;
    }

    /**
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * @param string $enterprizeCoupon
     */
    public function setEnterprizeCoupon($enterprizeCoupon) {
        $this->enterprizeCoupon = trim((string)$enterprizeCoupon);
    }

    /**
     * @return string
     */
    public function getEnterprizeCoupon() {
        return $this->enterprizeCoupon;
    }

    /**
     * @param boolean $agree
     */
    public function setAgree($agree) {
        $this->agree = (bool)$agree;
    }

    /**
     * @return boolean
     */
    public function getAgree() {
        return $this->agree;
    }

    /**
     * @param $name
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setError($name, $value) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        $this->errors[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getError($name) {
        if (!array_key_exists($name, $this->errors)) {
            throw new \InvalidArgumentException(sprintf('Неизвестная ошибка "%s".', $name));
        }

        return $this->errors[$name];
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid() {
        $isValid = true;
        foreach ($this->errors as $error) {
            if (null !== $error) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }
}