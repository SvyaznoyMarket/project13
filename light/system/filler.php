<?php

namespace light;

require_once 'aFillerObject.php';

class Filler
{
    private static $instance;
    private $filePath;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance()
    {
        if(empty(self::$instance))
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function get($fillerName)
    {
        $className = '\light\Filler' . ucfirst($fillerName);
        if(!class_exists($className) && file_exists($this->filePath . '/' . $fillerName . '.php'))
        {
            require_once $this->filePath . '/' . $fillerName . '.php';
        }

        return class_exists($className)?forward_static_call(array($className, 'getInstance')):Null;
    }
}