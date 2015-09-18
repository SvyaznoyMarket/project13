<?php

namespace Model\Point;

class WorkingTime {
    /** @var string */
    public $common = '';
    /** @var \Model\Point\WorkingTime\Days|null */
    public $days;

    function __construct($data = []) {
        if (isset($data['common'])) $this->common = (string)$data['common'];

        unset($data['common']);

        if ($data) {
            $this->days = new \Model\Point\WorkingTime\Days($data);
        }
    }
}