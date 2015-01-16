<?php

namespace Model\Seo\Hotlink;

class Entity {
    /** @var string|null */
    private $groupName;
    /** @var string|null */
    private $name;
    /** @var string|null */
    private $url;

    public function __construct(array $data = []) {
        if (isset($data['group'])) $this->setGroupName($data['group']);
        if (isset($data['name'])) $this->setName($data['name']);
        if (isset($data['url'])) $this->setUrl($data['url']);
    }

    /**
     * @param string $groupName
     */
    public function setGroupName($groupName) {
        $this->groupName = (string)$groupName;
    }

    /**
     * @return string
     */
    public function getGroupName() {
        return $this->groupName;
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
