<?php

namespace Model\DeliveryType;

class Entity {
    const TYPE_STANDART = 'standart';
    const TYPE_SELF = 'self';
    const TYPE_NOW = 'now';
    const TYPE_PICKPOINT = 'self_partner_pickpoint';
    const TYPE_PICKPOINT_ID = 6;

    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $shortName;
    /** @var string */
    private $description;
    /** @var array */
    private $methodTokens = [];
    /** @var string */
    private $buttonName;
    /**
     * Возможные токены методов доставки для данного типа доставки
     * @var array
     */
    private $possibleMethodTokens = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('short_name', $data)) $this->setShortName($data['short_name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('methods', $data)) $this->setMethodTokens((array)$data['methods']);
        if (array_key_exists('possible_method_tokens', $data)) $this->setPossibleMethodTokens((array)$data['possible_method_tokens']);
        if (array_key_exists('button_name', $data)) $this->setButtonName($data['button_name']);
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
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
     * @param string $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $shortName
     */
    public function setShortName($shortName) {
        $this->shortName = (string)$shortName;
    }

    /**
     * @return string
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * @param array $methodTokens
     */
    public function setMethodTokens(array $methodTokens) {
        $this->methodTokens = $methodTokens;
    }

    /**
     * @return array
     */
    public function getMethodTokens() {
        return $this->methodTokens;
    }

    /**
     * @param array $stateTokens
     */
    public function setPossibleMethodTokens(array $stateTokens) {
        $this->possibleMethodTokens = $stateTokens;
    }

    /**
     * @return array
     */
    public function getPossibleMethodTokens() {
        return $this->possibleMethodTokens;
    }

    /**
     * @param string $buttonName
     */
    public function setButtonName($buttonName) {
        $this->buttonName = (string)$buttonName;
    }

    /**
     * @return string
     */
    public function getButtonName() {
        return $this->buttonName;
    }
}