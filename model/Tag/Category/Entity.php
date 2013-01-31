<?php

namespace Model\Tag\Category;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var int */
    private $productCount;

    public function __construct(array $data = []) {
        if (array_key_exists('category_id', $data)) $this->setId($data['category_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('count', $data)) $this->setProductCount($data['count']);
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
     * @param int $productCount
     */
    public function setProductCount($productCount)
    {
        $this->productCount = (int)$productCount;
    }

    /**
     * @return int
     */
    public function getProductCount()
    {
        return $this->productCount;
    }
}