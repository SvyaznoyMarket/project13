<?php

namespace Model\Tag;

class Entity {

    const UID_SOBERI_SAM = 'f68c0fd6-7ed0-4d93-b3ff-cb4b8017f788';

    /** @var string */
    public $uid;
    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var Category\Entity[] */
    public $category;

    public function __construct(array $data = []) {
        if (array_key_exists('uid', $data)) $this->uid = $data['uid'];

        if (array_key_exists('id', $data)) $this->id = $data['id'];
        if (array_key_exists('core_id', $data)) $this->id = $data['core_id']; // scms

        if (array_key_exists('name', $data)) $this->name = $data['name'];

        if (array_key_exists('token', $data)) $this->token = $data['token'];
        if (array_key_exists('slug', $data)) $this->token = $data['token']; // scms

        if (array_key_exists('product', $data) && !empty($data['product']['category_statistics'])) {
            foreach ((array)$data['product']['category_statistics'] as $categoryData) {
                $this->addCategory(new Category\Entity($categoryData));
            }
        };
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getToken() {
        return $this->token;
    }

    /**
     * @param \Model\Tag\Category\Entity[] $categories
     */
    public function setCategory(array $categories) {
        $this->category = [];
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