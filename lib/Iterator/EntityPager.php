<?php

namespace Iterator;

class EntityPager implements \Iterator {
    /** @var array */
    private $collection = [];
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
        $this->collection = array_values($collection);
        $this->count = $count;
    }

    public function setPage($page) {
        $this->page = (int)$page;
        $this->calculateLastPage();
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
    public function setMaxPerPage($maxPerPage) {
        $maxPerPage = (int)$maxPerPage;
        if ($maxPerPage < 1) {
            $maxPerPage = 1;
        }
        $this->maxPerPage = $maxPerPage;
        $this->calculateLastPage();
    }

    /**
     * @return int
     */
    public function getMaxPerPage() {
        return $this->maxPerPage;
    }

    /**
     * @return bool
     */
    public function hasPages() {
        return $this->lastPage > 1;
    }

    private function calculateLastPage() {
        if (0 === (int) $this->maxPerPage) {
            $this->lastPage = 1;
            return false;
        }
        $this->lastPage = ceil($this->count / $this->maxPerPage);
        if ($this->lastPage < 1) $this->lastPage = 1;
    }

    public function count() {
        return $this->count;
    }

    public function setCount($count) {
        return $this->count = (int)$count;
    }

    public function rewind() {
        $this->position = 0;
    }

    public function current() {
        return $this->collection[$this->position];
    }

    /** Returns current position
     * @return int
     */
    public function key() {
        return $this->position;
    }

    public function next() {
        ++$this->position;
    }

    public function valid() {
        return isset($this->collection[$this->position]);
    }
}