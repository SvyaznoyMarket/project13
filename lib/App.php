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

            switch ($level) {
                case E_USER_ERROR:
                case E_WARNING:
                case E_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                case E_USER_WARNING:
                case E_USER_NOTICE:
                    if ((0 !== error_reporting()) && \App::logger()) {
                        \App::logger()->error(['message' => $message, 'sender' => $file . ' ' . $line, 'level' => isset($levels[$level]) ? $levels[$level] : null], ['critical', 'error_handler']);
                    }

                    return true;
            }

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
     * @param string|null $name Постфикс названия файла с правилами маршрутизации (обычно совпадает с названием приложения: main, mobile, ...)
     * @return \Routing\Router
     */
    public static function router($name = null) {
        static $instances = [];

        if (null == $name) {
            $name = self::$name;
        }

        if (!isset($instances[$name])) {
            $globalParams = [];
            if (\App::config()->debug) {
                $globalParams['parent_ri'] = \App::$id;
            }
            $rules = require self::$config->configDir . '/route-' . $name . '.php';
            $instances[$name] = new \Routing\Router($rules, self::$config->routeUrlPrefix, $globalParams);
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
     * @return \Search\Client
     */
    public static function searchClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Search\Client(self::$config->searchClient, self::curl());
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

    /** File storage client
     * @static
     * @return \Core\ClientV2
     */
    public static function fileStorageClient() {
        static $instance;
        $curl = clone self::curl();

        if (!$instance) {
            $curl->setNativePost();
            $instance = new \Core\ClientV2(self::config()->fileStorage, $curl);
        }

        return $instance;
    }
	
	
	/**
     * @static
     * @return \Core\ClientV2
     */
    public static function crmClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Core\ClientV2(self::config()->crm, self::curl());
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
            $instance = new \DataStore\Client();
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
     * @return \RichRelevance\Client
     */
    public static function richRelevanceClient()
    {
        static $instance;

        if (!$instance) {

            $config = [
                'apiUrl'        => self::config()->richRelevance['apiUrl'],
                'timeout'       => self::config()->richRelevance['timeout'],
            ];

            $instance = new \RichRelevance\Client($config, self::curl());
        }

        return $instance;
    }

    /**
     * @return \RetailRocket\Manager
     */
    public static function retailrocket() {
        static $instance;

        if (!$instance) {
            $instance = new \RetailRocket\Manager();
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
            } elseif (\Oauth\TwitterProvider::NAME == $name) {
                $instances[$name] = new \Oauth\TwitterProvider(self::$config->twitterOauth);
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

        // если есть вероятность нулевого журналирования, то ...
        if ($emptyChance = self::$config->logger['emptyChance']) {
            if (rand(0, 100) <= $emptyChance) {
                $name = 'empty';
            }
        }

        if (!isset(self::$loggers[$name])) {
            switch ($name) {
                case 'timer':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/timer.log', self::$config->logger['pretty']), $name, $config[$name]['level']);
                    break;
                case 'order':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/order.log', self::$config->logger['pretty']), $name, $config[$name]['level']);
                    break;
                case 'query':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/query.log', self::$config->logger['pretty']), $name, $config[$name]['level']);
                    break;
                case 'custom':
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/custom.log', self::$config->logger['pretty']), $name, $config[$name]['level']);
                    break;
                case 'empty':
                    self::$loggers[$name] = new \Logger\NullLogger();
                    break;
                default:
                    self::$loggers[$name] = new \Logger\DefaultLogger(new \Logger\Appender\FileAppender(self::$config->logDir . '/app.log', self::$config->logger['pretty']), $name, $config[$name]['level']);
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
     * @return \Session\AbTest\AbTest
     */
    public static function abTest() {
        static $instance;

        if (!$instance) {
            $instance = new \Session\AbTest\AbTest();
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
     * @return \Payment\SvyaznoyClubManager
     */
    public static function sclubManager() {
        static $instance;

        if (!$instance) {
            $instance = new \Payment\SvyaznoyClubManager();
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
                'template_class_prefix' => preg_replace('/[^\w]/', '_', \App::$config->mainHost . '-'),
                'cache'                 => (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache',
                'loader'                => new Mustache_Loader_FilesystemLoader(App::config()->templateDir),
                'partials_loader'       => new Mustache_Loader_FilesystemLoader(App::config()->templateDir),
                'escape'                => [new \Helper\TemplateHelper(), 'escape'],
                'charset'               => 'UTF-8',
                //'logger'                => null,
                'logger'                => new Mustache_Logger_StreamLogger('php://stderr'),
            ]);
        }

        return $instance;
    }    

    /**
     * @static
     * @return \Scms\Client
     */
    public static function scmsClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Scms\Client(self::config()->scms, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \Scms\Client
     */
    public static function scmsClientV2() {
        static $instance;

        if (!$instance) {
            $instance = new \Scms\Client(self::config()->scmsV2, self::curl());
        }

        return $instance;
    }


    /**
     * @static
     * @return \Scms\Client
     */
    public static function scmsSeoClient() {
        static $instance;

        if (!$instance) {
            $instance = new \Scms\Client(self::config()->scmsSeo, self::curl());
        }

        return $instance;
    }

    /**
     * @static
     * @return \Helper\TemplateHelper
     */
    public static function helper() {
        static $instance;

        if (!$instance) {
            $instance = new \Helper\TemplateHelper();
        }

        return $instance;
    }
}
