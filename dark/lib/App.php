<?php

class App {
    /** @var bool */
    private static $initialized = false;
    /** @var \AppConfig */
    private static $config;
    /** @var \Logger\LoggerInterface[] */
    private static $loggers = array();

    /**
     * @static
     * @param string $appDir Директория приложения
     */
    public static function init(\AppConfig $config) {
        self::$config = $config;

        if (self::$initialized) {
            throw new \LogicException('Приложение уже инициализировано.');
        }

        if (self::$config->debug) {
            ini_set('display_errors', 1);
            ini_set('html_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('html_errors', 0);
            error_reporting(E_ALL ^ E_NOTICE);
        }

        $libDir = self::$config->libDir;
        spl_autoload_register(function ($class) use ($libDir) {
            require_once $libDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        });

        set_error_handler(function ($level, $message, $file, $line, $context) {
            static $levels = [
                E_WARNING           => 'Warning',
                E_NOTICE            => 'Notice',
                E_USER_ERROR        => 'User Error',
                E_USER_WARNING      => 'User Warning',
                E_USER_NOTICE       => 'User Notice',
                E_STRICT            => 'Runtime Notice',
                E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
                E_DEPRECATED        => 'Deprecated',
                E_USER_DEPRECATED   => 'User Deprecated',
            ];

            if (error_reporting() & $level) {
                throw new \ErrorException(sprintf('%s: %s in %s line %d', isset($levels[$level]) ? $levels[$level] : $level, $message, $file, $line));
            }

            return false;
        });

        self::$initialized = true;
    }

    public static function shutdown() {
        foreach (self::$loggers as $logger) {
            $logger->dump();
        }
    }

    /**
     * @static
     * @return \AppConfig
     */
    public static function config() {
        return self::$config;
    }

    /**
     * @static
     * @return \Routing\Router
     */
    public static function router() {
        static $instance;

        if (!$instance) {
            $rules = require self::$config->configDir . '/route.php';

            $instance = new \Routing\Router($rules);
        }

        return $instance;
    }

    /**
     * @static
     * @return \Http\Request
     */
    public static function request() {
        static $instance;

        if (!$instance) {
            $instance = \Http\Request::createFromGlobals();
        }

        return $instance;
    }

    /**
     * @static
     * @return \Session\User
     */
    public static function user() {
        static $instance;

        if (!$instance) {
            $instance = new \Session\User();
        }

        return $instance;
    }

    /**
     * @param string $name
     * @return \Core\ClientV2
     */
    public static function coreClientV2() {
        static $instance;

        if (!$instance) {
            $instance = new \Core\ClientV2(self::$config->coreV2, \App::logger('core_v2'));
        }

        return $instance;
    }

    /**
     * @static
     * @return \Wordpress\Client
     */
    public static function contentClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Wordpress\Client();
            $instance->setUrl(\App::config()->wordpress['url']);
        }

        return $instance;
    }

    /**
     * @static
     * @param string $name Logger owner
     * @return \Logger\LoggerInterface
     */
    public static function logger($name = 'app') {
        static $config = array();

        if (!$config) {
            $config = require self::$config->configDir . '/logger.php';
        }

        if (!isset($config[$name])) {
            $config[$name] = $config['default'];
        }

        if (!isset(self::$loggers[$name])) {
            switch ($name) {
                case 'core_v1':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/core_v1.log'), $name, $config[$name]['level']);
                    break;
                case 'core_v2':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/core_v2.log'), $name, $config[$name]['level']);
                    break;
                case 'timer':
                    self::$loggers[$name] = new \Logger\TimerLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/timer.log'), $name, $config[$name]['level']);
                    break;
                default:
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/app.log'), $name, $config[$name]['level']);
                    //$instances[$name] = new \Logger\NullLogger();
                    break;
            }
        }

        return self::$loggers[$name];
    }
}
