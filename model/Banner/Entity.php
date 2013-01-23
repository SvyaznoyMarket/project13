<?php

namespace Model\Banner;

class Entity {
    /** @var int */
    private $id;
    /** @var int */
    private $typeId;
    /** @var string */
    private $name;
    /** @var string */
    private $url;
    /** @var string */
    private $image;
    /** @var Item\Entity[] */
    private $item = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('type_id', $data)) $this->setTypeId($data['type_id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
        if (array_key_exists('url', $data)) $this->setUrl($data['url']);
        if (array_key_exists('item_list', $data) && is_array($data['item_list'])) foreach ($data['item_list'] as $item) {
            $this->addItem(new Item\Entity($item));
        }
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
     * @param Item\Entity[] $items
     */
    public function setItem(array $items) {
        $this->item = [];
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    /**
     * @param Item\Entity $item
     */
    public function addItem(Item\Entity $item) {
        $this->item[] = $item;
    }

    public function getItem() {
        return $this->item;
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
     * @param int $typeId
     */
    public function setTypeId($typeId) {
        $this->typeId = (int)$typeId;
    }

    /**
     * @return int
     */
    public function getTypeId() {
        return $this->typeId;
    }

    /**
     * @param string $url
     */
    public function setUrl($url) {
        $this->url = (string)$url;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }
}