<?php

namespace Model\Point\WorkingTime\Days;

class Day {
    /** @var string */
    public $from = '';
    /** @var string */
    public $to = '';

    function __construct($data = []) {
        if (isset($data[0])) $this->from = (string)$data[0];
        if (isset($data[1])) $this->to = (string)$data[1];
    }
}