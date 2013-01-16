<?php

namespace Validator;

class Regex {
    /** @var string */
    public $pattern;
    /** @var bool */
    public $match = true;

    public function isValid($value) {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException('Значение должно быть типа "string".');
        }

        $value = (string)$value;

        if ($this->match xor preg_match($this->pattern, $value)) {
            return false;
        }

        return true;
    }
}
