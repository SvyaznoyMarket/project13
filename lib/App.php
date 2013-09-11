<?php

class App {
    /** @var string */
    public static $env;
    /** @var string */
    public static $id;
    /** @var string */
    public static $name = 'main';
    /** @var bool */
    private static $initialized = false;
    /** @var \Config\AppConfig */
    private static $config;
    /** @var \Logger\LoggerInterface[] */
    private static $loggers = [];

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

        if (self::$initialized) {
            throw new \LogicException('Приложение уже инициализировано.');
        }

        if (self::$config->debug) {
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            ini_set('display_errors', 0);
        }

        // error handler
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

        // shutdown handler
        register_shutdown_function($shutdownHandler ?: function() {
            \App::shutdown();
        });

        if (('terminal' == self::$name) && self::$config->loadMediaHost) {
            try {
                $data = \App::coreClientV2()->query('terminal/get', [
                    'ip'  => '0.0.0.0',
                    'uid' => '443ecd0b-d914-4c2e-b1ea-a190e6b2ffb6',
                ]);

                $imageUrl = isset($data['config']['image_host'])
                    ? ('http://' . trim($data['config']['image_host'], '/'))
                    : null;
                if ($imageUrl) {
                    self::$config->mediaHost = [$imageUrl];
                }
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }

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
     * @param string|null $name Постфикс названия файла с правилами маршрутизации (обычно совпадает с названием приложения: main, mobile, ...)
     * @return \Routing\Router
     */
    public static function router($name = null) {
        static $instances = [];

        if (null == $name) {
            $name = self::$name;
        }

        if (!isset($instances[$name])) {
            $rules = require self::$config->configDir . '/route-' . $name . '.php';
            $instances[$name] = new \Routing\Router($rules, self::$config->routePrefix);
        }

        return $instances[$name];
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
     * @return \Routing\ActionResolver
     */
    public static function actionResolver() {
        static $instance;

        if (!$instance) {
            $instance = new \Routing\ActionResolver(self::$config->controllerPrefix);
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
            $instance = new \Templating\PhpEngine(self::$config->templateDir);
        }

        return $instance;
    }

    /**
     * @static
     * @return Templating\PhpClosureEngine
     */
    public static function closureTemplating() {
        static $instance;

        if (!$instance) {
            $instance = new \Templating\PhpClosureEngine(self::$config->templateDir);
            $instance->setParam('helper', new \Helper\TemplateHelper());
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
            $instance = new \Curl\Client(\App::logger());
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
            $instance = new \Core\ClientV2(self::$config->coreV2, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \Core\ClientPrivate
     */
    public static function coreClientPrivate() {
        static $instance;

        if (!$instance) {
            $instance = new \Core\ClientPrivate(self::$config->corePrivate, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \ReviewsStore\Client
     */
    public static function reviewsClient() {
        static $instance;

        if (!$instance) {
            $instance = new \ReviewsStore\Client(self::config()->reviewsStore, self::curl());
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
            $instance = new \Content\Client(self::config()->wordpress, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \PDO
     */
    public static function database() {
        static $instance;

        if (!$instance) {
            try {
                $instance = new \PDO(sprintf('mysql:dbname=%s;host=%s', self::config()->database['name'], self::config()->database['host']), self::config()->database['user'], self::config()->database['password'], [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                ]);
            } catch (\Exception $e) {
                self::logger()->error($e);
            }
        }

        return $instance;
    }

    /**
     * @static
     * @return \DataStore\Client
     */
    public static function dataStoreClient() {
        static $instance;

        if (!$instance) {
            $instance = new \DataStore\Client(self::config()->dataStore, self::curl());
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
            $instance = new \Smartengine\Client(self::$config->smartengine, \App::logger());
        }

        return $instance;
    }

    /**
     * @static
     * @return \RetailRocket\Client
     */
    public static function retailrocketClient() {
        static $instance;

        if (!$instance) {
            $instance = new \RetailRocket\Client(self::$config->partners['RetailRocket'], \App::logger());
        }

        return $instance;
    }

    /**
     * @param $name
     * @return \Oauth\ProviderInterface
     * @throws InvalidArgumentException
     */
    public static function oauth($name) {
        static $instances = [];

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
        static $config = [];

        if (!$config) {
            $config = require self::$config->configDir . '/logger.php';
        }

        if (!isset($config[$name])) {
            $config[$name] = $config['default'];
        }

        if (!isset(self::$loggers[$name])) {
            switch ($name) {
                case 'timer':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/timer.log'), $name, $config[$name]['level']);
                    break;
                case 'request_compatible':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/site_page_time.log'), 'RequestLogger', $config[$name]['level']);
                    break;
                case 'order':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/order.log'), $name, $config[$name]['level']);
                    break;
                default:
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/app.log'), $name, $config[$name]['level']);
                    //$instances[$name] = new \Logger\NullLogger();
                    break;
            }
        }

        return self::$loggers[$name];
    }

    /**
     * @return \Debug\Collector
     */
    public static function debug() {
        static $instance;

        if (!$instance) {
            $instance = new \Debug\Collector();
        }

        return $instance;
    }

    /**
     * @return \Session\Abtest
     */
    public static function abTest() {
        static $instance;

        if (!$instance) {
            $instance = new \Session\Abtest(self::config()->abtest);
        }

        return $instance;
    }

    /**
     * @return \Partner\Manager
     */
    public static function partner() {
        static $instance;

        if (!$instance) {
            $instance = new \Partner\Manager();
        }

        return $instance;
    }

    /**
     * @return Mustache_Engine
     */
    public static function mustache() {
        static $instance;

        if (!$instance) {
            require \App::config()->appDir . '/vendor/mustache/src/Mustache/Autoloader.php';
            Mustache_Autoloader::register(\App::config()->appDir . '/vendor/mustache/src');
            $instance = new Mustache_Engine([
                'cache'           => (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache',
                'loader'          => new Mustache_Loader_FilesystemLoader(App::config()->templateDir),
                'partials_loader' => new Mustache_Loader_FilesystemLoader(App::config()->templateDir),
                'escape'          => [new \Helper\TemplateHelper(), 'escape'],
                'charset'         => 'UTF-8',
                'logger'          => null,
            ]);
        }

        return $instance;
    }
}
