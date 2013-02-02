<?php

namespace Model\Page;

class Entity {
    /** @var string */
    private $path;
    /** @var string */
    private $title;
    /** @var string */
    private $header;
    /** @var string */
    private $keywords;
    /** @var string */
    private $description;
    /** @var array */
    private $content;

    public function __construct(array $data = []) {
        if (array_key_exists('path', $data)) $this->setPath($data['path']);
        if (array_key_exists('title', $data)) $this->setTitle($data['title']);
        if (array_key_exists('header', $data)) $this->setHeader($data['header']);
        if (array_key_exists('keywords', $data)) $this->setKeywords($data['keywords']);
        if (array_key_exists('description', $data)) $this->setDescription($data['description']);
        if (array_key_exists('content', $data) && is_array($data['content'])) $this->setContent($data['content']);
    }

    /**
     * @param array $content
     */
    public function setContent(array $content) {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = (string)$description;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $header
     */
    public function setHeader($header) {
        $this->header = (string)$header;
    }

    /**
     * @return string
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords) {
        $this->keywords = (string)$keywords;
    }

    /**
     * @return string
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = (string)$path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = (string)$title;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }
}