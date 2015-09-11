<?php

namespace Model\Promo;

class Entity {
    /** @var string */
    private $token;
    /** @var string */
    private $name;
    /** @var \Model\Promo\Page\Entity[] */
    private $pages;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (isset($data['pages'][0])) $this->setPages($data['pages']);
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
     * @param array $pages
     */
    public function setPages(array $pages) {
        foreach ($pages as $item) {
            if (!isset($item['uid'])) continue;

            $this->pages[] = new \Model\Promo\Page\Entity($item);
        }
    }

    /**
     * @return \Model\Promo\Page\Entity[]
     */
    public function getPages() {
        return $this->pages;
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
}