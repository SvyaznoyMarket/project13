<?php

namespace Validator;

class Date {
    const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})$/';

    public function isValid($value) {
        if (null === $value || '' === $value) {
            return true;
        }

        if ($value instanceof \DateTime) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException('Значение должно быть типа "string".');
        }

        $value = (string)$value;

        if (!preg_match(static::PATTERN, $value, $matches) || !checkdate($matches[2], $matches[3], $matches[1])) {
            return false;
        }

        return true;
    }
}
