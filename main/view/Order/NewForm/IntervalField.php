<?php

namespace View\Order\NewForm;

class IntervalField {
    /** @var string */
    private $startAt;
    /** @var string */
    private $endAt;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        $data = array_values(array_pad($data, 2, null));

        $this->setStartAt($data[0]);
        $this->setEndAt($data[1]);
    }

    /**
     * @param string $endAt
     */
    public function setEndAt($endAt) {
        $this->endAt = (string)$endAt;
    }

    /**
     * @return string
     */
    public function getEndAt() {
        return $this->endAt;
    }

    /**
     * @param string $startAt
     */
    public function setStartAt($startAt) {
        $this->startAt = (string)$startAt;
    }

    /**
     * @return string
     */
    public function getStartAt() {
        return $this->startAt;
    }
}