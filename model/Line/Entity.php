<?php

namespace Model\Line;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var string */
    private $image;
    /** @var string */
    private $description;
    /** @var int */
    private $mainProductId;
    /** @var array */
    private $productId = array();
    /** @var array */
    private $kitId = array();

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('product_id_list', $data)) $this->setProductId($data['product_id_list']);
        if (array_key_exists('kit_id_list', $data)) $this->setKitId($data['kit_id_list']);
        if (array_key_exists('main_product_id', $data)) $this->setMainProductId($data['main_product_id']);
    }

    public function setId($id) {
        $this->id = (int)$id;
    }

    public function getId() {
        return $this->id;
    }

    public function setName($name) {
        $this->name = (string)$name;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @param string $image
     */
    public function setImage($image) {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage() {
        return $this->image;
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
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $kitId
     */
    public function setKitId(array $kitId)
    {
        $this->kitId = $kitId;
    }

    /**
     * @return array
     */
    public function getKitId()
    {
        return $this->kitId;
    }

    /**
     * @param int $mainProductId
     */
    public function setMainProductId($mainProductId)
    {
        $this->mainProductId = (int)$mainProductId;
    }

    /**
     * @return int
     */
    public function getMainProductId()
    {
        return $this->mainProductId;
    }

    /**
     * @param array $productId
     */
    public function setProductId(array $productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return array
     */
    public function getProductId()
    {
        return $this->productId;
    }
}
