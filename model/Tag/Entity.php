<?php

namespace Model\Tag;

class Entity {
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $token;
    /** @var Category\Entity[] */
    private $category;

    public function __construct(array $data = array()) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('product', $data) && !empty($data['product']['category_statistics'])) {
            foreach ((array)$data['product']['category_statistics'] as $categoryData) {
                $this->addCategory(new Category\Entity($categoryData));
            }
        };
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

    public function setToken($token) {
        $this->token = (string)$token;
    }

    public function getToken() {
        return $this->token;
    }

    /**
     * @param \Model\Tag\Category\Entity[] $categories
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
     * @return \Model\Tag\Category\Entity[]
     */
    public function getCategory() {
        return $this->category;
    }
}