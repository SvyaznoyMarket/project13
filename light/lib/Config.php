<?php

namespace light;

/**
 * Класс реализующий интерфейс взаимодействия с конфигурацией приложения
 */
class Config
{
    private static $instance;
    private static $environment;
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

    public static function getEnvironment(){
        if(!self::$environment){
            $env = isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'loc';

            self::$environment = $env;
        }

        return self::$environment;
    }

    public static function get($index = Null)
    {
        $value = self::$instance->parameterList;

        if(!empty($index))
        {
            $indexPartList = explode('.', $index);

            foreach($indexPartList as $indexPart)
            {
                if(!in_array($indexPart, array_keys($value)))
                {
                    throw new \Exception('Config parameter "' . $index . '" does not exists');
                }

                $value = $value[$indexPart];
            }
        }


        return $value;
    }

    public static function isDebugMode()
    {
        if(isset($_COOKIE['debug']) && $_COOKIE['debug'] == 'site')
        {
            return True;
        }

        return False;
    }
}