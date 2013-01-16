<?php

namespace Validator;

class Min {
    /** @var int */
    public $limit;

    public function isValid($value) {
        if (null === $value) {
            return true;
        }

        if (!is_numeric($value)) {
            return false;
        }

        if ($value < $this->limit) {
            return false;
        }

        return true;
    }
}
