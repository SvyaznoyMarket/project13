<?php

namespace Model\Promo\Image;

class Entity {
    const ACTION_LINK = 'link';
    const ACTION_PRODUCT = 'product';
    const ACTION_PRODUCT_CATEGORY = 'catalog';

    /** @var string */
    private $name;
    /** @var string */
    private $url;
    /** @var string */
    private $action;
    /** @var array */
    private $item = [];
    /** @var string */
    private $link;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('url', $data)) $this->setUrl($data['url']);
        if (array_key_exists('action', $data)) $this->setAction($data['action']);
        if (array_key_exists('item', $data)) {
            if (!is_array($data['item'])) {
                $data['item'] = [$data['item']];
            }
            $this->setItem($data['item']);
        }
    }

    /**
     * @param string $action
     */
    public function setAction($action) {
        $this->action = (string)$action;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @param array $item
     */
    public function setItem(array $item) {
        $this->item = $item;
    }

    /**
     * @return array
     */
    public function getItem() {
        return $this->item;
    }

    /**
     * @param string $link
     */
    public function setLink($link) {
        $this->link = (string)$link;
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