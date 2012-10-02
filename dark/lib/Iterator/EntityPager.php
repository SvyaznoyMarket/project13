<?php

namespace Iterator;

class EntityPager implements \Iterator {
    /** @var array */
    private $collection = array();
    /** @var int */
    private $count = 0;
    /** @var int */
    private $position = 0;

    /** @var int */
    private $page = 1;
    /** @var int */
    private $lastPage = 1;
    /** @var int */
    private $maxPerPage = 10;

    public function __construct(array $collection, $count) {
        $this->collection = $collection;
        $this->count = $count;
    }

    public function setPage($page) {
        $this->page = (int)$page;
        $this->lastPage = ceil($this->count / $this->maxPerPage);
    }

    public function getPage() {
        return $this->page;
    }

    public function getLastPage() {
        return $this->lastPage;
    }

    /**
     * @param int $maxPerPage
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->maxPerPage = (int)$maxPerPage;
    }

    /**
     * @return int
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * @return bool
     */
    public function hasPages() {
        return $this->lastPage > 1;
    }

    public function count() {
        return $this->count;
    }

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->collection[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->collection[$this->position]);
    }
}