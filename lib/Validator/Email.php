<?php

namespace Validator;

class Email {
    /** @var bool */
    public $checkMX = false;

    public function isValid($value) {
        if (null === $value || '' === $value) {
            return true;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException('Значение должно быть типа "string".');
        }

        $value = (string)$value;
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);

        if ($valid) {
            $host = substr($value, strpos($value, '@') + 1);

            // Check MX records
            if ($valid && $this->checkMX) {
                $valid = $this->checkMX($host);
            }
        }

        if (!$valid) {
            return false;
        }

        return true;
    }

    /**
     * Check DNS Records for MX type.
     *
     * @param string $host Host name
     *
     * @return bool
     */
    private function checkMX($host) {
        return checkdnsrr($host, 'MX');
    }
}
