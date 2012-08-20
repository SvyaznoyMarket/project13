<?php

namespace light;

abstract class FillerObject
{
    private static $instance;

    public static function getInstance()
    {
        if(empty(self::$instance))
        {
            self::$instance = new static;
        }

        return self::$instance;
    }

    abstract public function run();
}