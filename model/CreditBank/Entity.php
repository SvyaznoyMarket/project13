<?php

namespace Model\CreditBank;

class Entity {

    /**
     * Кредитный брокет Kupivkredit
     */
    const PROVIDER_KUPIVKREDIT = 1;

    /**
     * Кредитный брокет Direct Credit
     */
    const PROVIDER_DIRECT_CREDIT = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $provider_id;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $description;


    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('href', $data)) $this->setLink($data['href']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('provider_id', $data)) $this->setProviderId($data['provider_id']);
        if (array_key_exists('position', $data)) $this->setPosition($data['position']);
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
     * @param int $provider_id
     */
    public function setProviderId($provider_id) {
        $this->provider_id = (int)$provider_id;
    }

    /**
     * @return int
     */
    public function getProviderId() {
        return $this->provider_id;
    }

    /**
     * @param int $position
     */
    public function setPosition($position) {
        $this->position = (int)$position;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @param int $token
     */
    public function setToken($token) {
        $this->token = (string)$token;
    }

    /**
     * @return int
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
    }

    /**
     * @return int
     */
    public function getLink() {
        return $this->link;
    }
}
