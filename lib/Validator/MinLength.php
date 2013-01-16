<?php

namespace Validator;

class MinLength {
    /** @var int */
    public $limit;
    /** @var string */
    public $charset = 'UTF-8';

    public function isValid($value) {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException('Значение должно быть типа "string".');
        }

        $value = (string)$value;

        if (function_exists('grapheme_strlen') && 'UTF-8' === $this->charset) {
            $length = grapheme_strlen($value);
        } elseif (function_exists('mb_strlen')) {
            $length = mb_strlen($value, $this->charset);
        } else {
            $length = strlen($value);
        }

        if ($length < $this->limit) {
            return false;
        }

        return true;
    }
}
