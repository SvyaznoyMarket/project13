<?php

namespace Model\Promo\Page;

class Entity {
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT = 'product';
    const ACTION_PRODUCT_CATEGORY = 'catalog';

    /** @var string */
    public $ui;
    /** @var string */
    private $name;
    /** @var string */
    private $imageUrl;
    /** @var string */
    private $link;
    /**
     * Время показа [мс]
     * @var int
     */
    private $time;
    /** @var array */
    private $products = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('uid', $data)) $this->ui = $data['uid'] ? (string)$data['uid'] : null;
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (isset($data['medias'][0])) $this->setImageUrl($data['medias']); // TODO: обработка media
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('time_to_display', $data)) $this->setTime($data['time_to_display']);
        if (isset($data['products'][0])) {
            $this->setProducts($data['products']);
        }
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        if (isset($link['url'])) {
            $this->link = (string)$link['url'];
        } else if (isset($link['category']['url'])) {
            $this->link = (string)$link['category']['url'];
        } else if (isset($link['slice']['url'])) {
            $this->link = (string)$link['slice']['url'];
        } else if (isset($link['static_page']['token'])) {
            $this->link = '/' . (string)$link['static_page']['token'];
        } else if (is_string($link)) {
            $this->link = $link['url'];
        } else if (isset($link['product']['url'])) {
            $this->link = (string)$link['product']['url'];
        }
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
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
     * @param array $medias
     */
    public function setImageUrl($medias) {
        foreach ($medias as $media) {
            if (!is_array($media)) {
                continue;
            }

            $mediaModel = new \Model\Media($media);
            $source = $mediaModel->getSource('original');

            if ($source) {
                $this->imageUrl = (string)$source->url;
            }
        }
    }

    /**
     * @return string
     */
    public function getImageUrl() {
        return $this->imageUrl;
    }

    /**
     * @param int $time
     */
    public function setTime($time) {
        $this->time = (int)$time;
    }

    /**
     * @return int
     */
    public function getTime() {
        return $this->time;
    }

    /**
     * @param array $products
     */
    public function setProducts(array $products) {
        foreach ($products as $item) {
            if (!isset($item['uid'])) continue;

            $this->products[] = new \Model\Product\Entity($item);
        }
    }

    /**
     * @return \Model\Product\Entity[]
     */
    public function getProducts() {
        return $this->products;
    }
}