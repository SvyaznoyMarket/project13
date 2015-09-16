<?php

namespace EnterQuery;

class Exception extends \Exception {
    /** @var array */
    private $detail = [];
    /** @var array */
    private $query;

    /**
     * @return array
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @param array $detail
     */
    public function setDetail(array $detail)
    {
        $this->detail = $detail;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }
}