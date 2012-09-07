<?php

namespace light;

/**
 * Класс реализующий интерфейс взаимодействия с конфигурацией приложения
 */
class Config
{
    private static $instance;
    private $parameterList;

    private function __construct($parameterList)
    {
        $this->parameterList = $parameterList;
    }

    private function __clone() {}

    public static function init(array $parameterList)
    {
        if(empty(self::$instance))
        {
            self::$instance = new self($parameterList);
        }
    }

    public static function get($index)
    {
        $indexPartList = explode('.', $index);
        $value = self::$instance->parameterList;

        foreach($indexPartList as $indexPart)
        {
            if(!in_array($indexPart, array_keys($value)))
            {
                throw new \Exception('Config parameter "' . $index . '" does not exists');
            }

            $value = $value[$indexPart];
        }

        return $value;
    }
}