<?php

class Autoloader {
    /**
     * @param string $basePath
     */
    public static function register($basePath) {
        spl_autoload_register(function ($class) use ($basePath) {
            if (0 === strpos($class, 'Mustache')) {
                return;
            }

            if ('\\' == $class[0]) {
                $class = substr($class, 1);
            }

            $namespace = substr($class, 0, strpos($class, '\\'));
            $path = null;
            switch ($namespace) {
                case 'Controller': case 'View':
                $class = lcfirst($class);
                $path = $basePath . '/main';
                break;
                case 'Model':
                    $class = preg_replace('/^' . $namespace . '/', '', $class);
                    $path = $basePath . '/model';
                    break;
                default:
                    $path = $basePath . '/lib';
            }

            require_once $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        });
    }
}