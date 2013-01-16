<?php

namespace Model\Product\Service;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $token;
    /** @var string */
    private $description;
    /** @var string */
    private $work;
    /** @var string */
    private $image;
    /** @var bool */
    private $isDelivered;
    /** @var bool */
    private $isInShop;
    /** @var int|null */
    private $price;
    /** @var int|null */
    private $pricePercent;
    /** @var int|null */
    private $priceMin;
    /** @var array Category\Entity[] */
    private $category = array();
    /** @var array Entity[] */
    private $alike = array();
    /** @var array */
    private $alikeId = array();

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('work', $data)) $this->setWork($data['work']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('is_delivery', $data)) $this->setIsDelivered($data['is_delivery']);
        // поддержка /v2/service/get2
        if (array_key_exists('is_in_shop', $data)) $this->setIsInShop($data['is_in_shop']);
        if (array_key_exists('only_inshop', $data)) $this->setIsInShop($data['only_inshop']);
        if (array_key_exists('price', $data)) $this->setPrice($data['price']);
        if (array_key_exists('price_percent', $data)) $this->setPricePercent($data['price_percent']);
        if (array_key_exists('price_min', $data)) $this->setPriceMin($data['price_min']);
        // поддержка /v2/service/get2
        if (array_key_exists('category_list', $data) && is_array($data['category_list'])) foreach ($data['category_list'] as $categoryData) {
            $this->addCategory(new Category\Entity($categoryData));
        }
        if (array_key_exists('category', $data) && is_array($data['category'])) foreach ($data['category'] as $categoryData) {
            $this->addCategory(new Category\Entity($categoryData));
        }
        // поддержка /v2/service/get2
        if (array_key_exists('alike_list', $data) && is_array($data['alike_list'])) $this->setAlikeId($data['alike_list']);
        if (array_key_exists('alike', $data) && is_array($data['alike'])) $this->setAlikeId($data['alike']);
    }

    /**
     * @param Entity[] $alikes
     */
    public function setAlike($alikes) {
        $this->category = array();
        foreach ($alikes as $alike) {
            $this->addAlike($alike);
        }
    }

    /**
     * @param Entity $alike
     */
    public function addAlike(Entity $alike) {
        $this->alike[] = $alike;
    }

    /**
     * @return Entity[]
     */
    public function getAlike() {
        return $this->alike;
    }

    /**
     * @param array $alikeId
     */
    public function setAlikeId(array $alikeId) {
        $this->alikeId = $alikeId;
    }

    /**
     * @return array
     */
    public function getAlikeId() {
        return $this->alikeId;
    }

    /**
     * @param Category\Entity[] $categories
     */
    public function setCategory(array $categories) {
        $this->category = array();
        foreach ($categories as $category) {
            $this->addCategory($category);
        }
    }

    /**
     * @param Category\Entity $category
     */
    public function addCategory(Category\Entity $category) {
        $this->category[] = $category;
    }

    /**
     * @return Category\Entity[]
     */
    public function getCategory() {
        return $this->category;
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
     * @param int $size
     * @return null|string
     */
    public function getImageUrl($size = 1) {
        return $this->image ? \App::config()->service['url'][$size] . $this->image : null;
    }

    /**
     * @param boolean $isDelivered
     */
    public function setIsDelivered($isDelivered) {
        $this->isDelivered = (bool)$isDelivered;
    }

    /**
     * @return boolean
     */
    public function getIsDelivered() {
        return $this->isDelivered;
    }

    /**
     * @param boolean $isInShop
     */
    public function setIsInShop($isInShop) {
        $this->isInShop = (bool)$isInShop;
    }

    /**
     * @return boolean
     */
    public function getIsInShop() {
        return $this->isInShop;
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
     * @param int $price
     */
    public function setPrice($price) {
        if (!is_null($price)) {
            $this->price = (int)$price;
        } else {
            $this->price = $price;
        }
    }

    /**
     * @return int|null
     */
    public function getPrice() {
        return $this->price;
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
     * @param string $work
     */
    public function setWork($work) {
        $this->work = (string)$work;
    }

    /**
     * @return string
     */
    public function getWork() {
        return $this->work;
    }

    /**
     * @return bool
     */
    public function isInSale() {
        return $this->getIsDelivered() && $this->getPrice() && $this->getPrice() >= \App::config()->service['minPriceForDelivery'];
    }

    /**
     * @param int $priceMin
     */
    public function setPriceMin($priceMin)
    {
        if (!is_null($priceMin)) {
            $this->priceMin = (int)$priceMin > 0 ? (int)$priceMin : null;
        } else {
            $this->priceMin = $priceMin;
        }
    }

    /**
     * @return int|null
     */
    public function getPriceMin()
    {
        return $this->priceMin;
    }

    /**
     * @param int $pricePercent
     */
    public function setPricePercent($pricePercent)
    {
        if (!is_null($pricePercent)) {
            $this->pricePercent = (int)$pricePercent > 0 ? (int)$pricePercent : null;
        } else {
            $this->pricePercent = $pricePercent;
        }
    }

    /**
     * @return int|null
     */
    public function getPricePercent()
    {
        return $this->pricePercent;
    }
}
