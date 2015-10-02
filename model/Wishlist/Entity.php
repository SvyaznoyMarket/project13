<?php

namespace Model\Wishlist;

class Entity {
    /** @var string */
    public $id;
    /** @var string */
    public $token;
    /** @var string */
    public $title;
    /** @var string */
    public $description;
    /** @var \Model\Wishlist\Product\Entity[] */
    public $products = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->id = (string)$data['id'];
        if (array_key_exists('token', $data)) $this->id = (string)$data['token'];
        if (array_key_exists('title', $data)) $this->title = (string)$data['title'];
        if (array_key_exists('description', $data)) $this->description = $data['description'] ? (string)$data['description'] : null;
        if (isset($data['products'][0])) {
            foreach ($data['products'] as $productItem) {
                if (!isset($productItem['uid'])) continue;
                $this->products[] = new \Model\Wishlist\Product\Entity($productItem);
            }
        }
    }
}