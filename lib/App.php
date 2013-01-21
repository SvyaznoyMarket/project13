<?php

class App {
    /** @var string */
    public static $env;
    /** @var string */
    public static $id;
    /** @var bool */
    private static $initialized = false;
    /** @var \Config\AppConfig */
    private static $config;
    /** @var \Logger\LoggerInterface[] */
    private static $loggers = array();

    /**
     * @param string           $env             Среда выполнения [local, dev, prod, ...]
     * @param Config\AppConfig $config
     * @param null             $shutdownHandler Функция, которая будет выполнена после завершения работы скрипта
     * @throws LogicException
     * @throws ErrorException
     */
    public static function init($env, \Config\AppConfig $config, $shutdownHandler = null) {
        self::$env = $env;
        self::$id = uniqid();
        self::$config = $config;

        mb_internal_encoding(self::$config->encoding ?: 'UTF-8');
        //setlocale(LC_ALL, array('ru_RU.UTF-8', 'rus_RUS.UTF-8', 'Russian_Russia.65001'));

        if (self::$initialized) {
            throw new \LogicException('Приложение уже инициализировано.');
        }

        if (self::$config->debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            ini_set('display_errors', 0);
        }

        $appDir = self::config()->appDir;
        spl_autoload_register(function ($class) use ($appDir) {
            if ('\\' == $class[0]) {
                $class = substr($class, 1);
            }

            $namespace = substr($class, 0, strpos($class, '\\'));
            $path = null;
            switch ($namespace) {
                case 'Controller': case 'View':
                $class = lcfirst($class);
                $path = $appDir . '/main';
                break;
                case 'Model':
                    $class = preg_replace('/^' . $namespace . '/', '', $class);
                    $path = $appDir . '/model';
                    break;
                default:
                    $path = $appDir . '/lib';
            }

            require_once $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
        });

        // error handler
        set_error_handler(function ($level, $message, $file, $line, $context) {
            static $levels = array(
                E_WARNING           => 'Warning',
                E_NOTICE            => 'Notice',
                E_USER_ERROR        => 'User Error',
                E_USER_WARNING      => 'User Warning',
                E_USER_NOTICE       => 'User Notice',
                E_STRICT            => 'Runtime Notice',
                E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
                E_DEPRECATED        => 'Deprecated',
                E_USER_DEPRECATED   => 'User Deprecated',
            );

            if (error_reporting() & $level) {
                throw new \ErrorException(sprintf('%s: %s in %s line %d', isset($levels[$level]) ? $levels[$level] : $level, $message, $file, $line));
            }

            return false;
        });

        // shutdown handler
        register_shutdown_function($shutdownHandler ?: function() {
            \App::shutdown();
        });

        self::$initialized = true;
    }

    public static function shutdown() {
        foreach (\Debug\Timer::getAll() as $timerName => $timer) {
            self::logger('timer')->info($timerName . ' ' . $timer['total'] . ' [' . $timer['count'] . ']');
        }

        foreach (self::$loggers as $logger) {
            $logger->dump();
        }
    }

    /**
     * @static
     * @return \Config\AppConfig
     */
    public static function config() {
        return self::$config;
    }

    /**
     * @return ExceptionStack
     */
    public static function exception() {
        static $instance;

        if (!$instance) {
            $instance = new \ExceptionStack();
        }

        return $instance;
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
     * @return \Http\Session
     */
    public static function session() {
        static $instance;

        if (!$instance) {
            $instance = new \Http\Session();
            $instance->start();
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
     * @static
     * @return Templating\PhpEngine
     */
    public static function templating() {
        static $instance;

        if (!$instance) {
            $instance = new \Templating\PhpEngine(self::config()->appDir . '/main/template');
        }

        return $instance;
    }

    /**
     * @static
     * @return Curl\Client
     */
    public static function curl() {
        static $instance;

        if (!$instance) {
            $instance = new \Curl\Client();
        }

        return $instance;
    }

    /**
     * @static
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
     * @return \Content\Client
     */
    public static function contentClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Content\Client(\App::config()->wordpress, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \Smartengine\Client
     */
    public static function smartengineClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Smartengine\Client(self::$config->smartEngine, \App::logger('smartengine'));
        }

        return $instance;
    }

    /**
     * @param $name
     * @return \Oauth\ProviderInterface
     * @throws InvalidArgumentException
     */
    public static function oauth($name) {
        static $instances = array();

        if (!isset($instances[$name])) {
            if (\Oauth\VkontakteProvider::NAME == $name) {
                $instances[$name] = new \Oauth\VkontakteProvider(self::$config->vkontakteOauth);
            } elseif (\Oauth\OdnoklassnikiProvider::NAME == $name) {
                $instances[$name] = new \Oauth\OdnoklassnikiProvider(self::$config->odnoklassnikiOauth);
            } elseif (\Oauth\FacebookProvider::NAME == $name) {
                $instances[$name] = new \Oauth\FacebookProvider(self::$config->facebookOauth);
            } else {
                throw new \InvalidArgumentException(sprintf('Не найден провайдер аутентификации "%s".', $name));
            }
        }

        return $instances[$name];
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
                case 'core_v2':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/core_v2.log'), $name, $config[$name]['level']);
                    break;
                case 'timer':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/timer.log'), $name, $config[$name]['level']);
                    break;
                case 'request_compatible':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/site_page_time.log'), 'RequestLogger', $config[$name]['level']);
                    break;
                case 'smartengine':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/smartengine.log'), $name, $config[$name]['level']);
                    break;
                default:
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/app.log'), $name, $config[$name]['level']);
                    //$instances[$name] = new \Logger\NullLogger();
                    break;
            }
        }

        return self::$loggers[$name];
    }

    public static function debug() {
        static $instance;

        if (!$instance) {
            $instance = new \Debug\Collector();
        }

        return $instance;
    }
}
