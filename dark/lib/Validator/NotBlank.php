<?php

namespace Validator;

class NotBlank {
    public function isValid($value) {
        if (false === $value || (empty($value) && '0' != $value)) {
            return false;
        }

        return true;
    }
}
