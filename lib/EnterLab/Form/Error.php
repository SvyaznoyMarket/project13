<?php

namespace EnterLab\Form;

class Error
{
    const CODE_INVALID = 'invalid';

    /** @var string */
    public $code;
    /** @var string */
    public $message;
    /** @var string */
    public $field;
}