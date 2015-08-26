<?php

namespace EnterModel;

use EnterModel as Model;

class Error extends Model\Entity {
    /** @var string */
    public $id;
    /** @var int */
    public $code = 0;
    /** @var string */
    public $message;
}