<?php

namespace Model\Search\Category;

class Entity {
    /* @var int */
    private $id;
    /* @var string */
    private $token;
    /* @var string */
    private $name;
    /* @var string */
    /* @var string */
    private $image;
    /** @var string */
    private $link;

    public function __construct(array $data = []) {
        if (array_key_exists('id', $data)) $this->setId($data['id']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('link', $data)) $this->setLink($data['link']);
        if (array_key_exists('token', $data)) $this->setToken($data['token']);
        if (array_key_exists('image', $data)) $this->setImage($data['image']);
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = (string)$image;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = (string)$link;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return rtrim($this->link, '/');
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = (string)$token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


}