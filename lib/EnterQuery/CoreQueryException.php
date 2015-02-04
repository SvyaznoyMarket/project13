<?php

namespace EnterQuery;

class CoreQueryException extends \Exception {
    /** @var array */
    private $detail = [];

    /**
     * @param array $detail
     */
    public function setDetail(array $detail) {
        $this->detail = $detail;
    }

    /**
     * @return array
     */
    public function getDetail() {
        return $this->detail;
    }
}