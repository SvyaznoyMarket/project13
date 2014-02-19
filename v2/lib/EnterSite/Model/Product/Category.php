<?php

namespace EnterSite\Model\Product;

use EnterSite\Model\ImportArrayConstructorTrait;

class Category {
    use ImportArrayConstructorTrait;

    /** @var string */
    public $id;
    /** @var string */
    public $parentId;
    /** @var string */
    public $name;
    /** @var string */
    public $token;
    /** @var string */
    public $link;
    /** @var string */
    public $image;
    /** @var int */
    public $level;
    /** @var bool */
    public $hasChildren;
    /** @var string */
    public $redirectLink;


    /**
     * @param array $data
     */
    public function import(array $data) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('parent_id', $data)) $this->parentId = (string)$data['parent_id'];
        if (array_key_exists('name', $data)) $this->name = (string)$data['name'];
        if (array_key_exists('token', $data)) $this->token = (string)$data['token'];
        if (array_key_exists('link', $data)) $this->link = rtrim((string)$data['link'], '/');
        if (array_key_exists('media_image', $data)) $this->image = (string)$data['media_image'];
        if (array_key_exists('level', $data)) $this->level = (int)$data['level'];
        if (array_key_exists('has_children', $data)) $this->hasChildren = (bool)$data['has_children'];
        if (!empty($data['redirect']['link'])) $this->redirectLink = (string)$data['redirect']['link'];
    }
}