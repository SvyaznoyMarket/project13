<?php

namespace EnterQuery;

class Callback
{
    /**
     * Обработчик
     * @var callable
     */
    public $handler;
    /**
     * @var \Exception|null
     */
    public $error;
}