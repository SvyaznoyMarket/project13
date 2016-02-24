<?php

namespace EnterLab\Form;

class Field
{
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_DATE = 'date';
    const TYPE_ARRAY = 'array';
    const TYPE_MOBILE_PHONE_NUMBER = 'mobile_phone_number';

    /**
     * Например, email
     *
     * @var string
     */
    public $name;
    /**
     * Например, green@mail.ru
     *
     * @var string
     */
    public $value;
    /**
     * Например, string
     *
     * @var string
     */
    public $type = 'string';
    /**
     * @var Error
     */
    public $error;

    /**
     * @return string
     */
    public function __toString()
    {
        return is_scalar($this->value) ? (string)$this->value : json_encode($this->value, JSON_UNESCAPED_UNICODE);
    }
}