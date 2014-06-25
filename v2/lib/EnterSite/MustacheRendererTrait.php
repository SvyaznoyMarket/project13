<?php

namespace EnterSite;

use Enter\Mustache\Loader\FilesystemAliasLoader;

trait MustacheRendererTrait {
    use ConfigTrait;

    /**
     * @return \Mustache_Engine
     */
    protected function getRenderer() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = $this->getConfig()->mustacheRenderer;

            require_once $config->dir . '/src/Mustache/Autoloader.php';
            \Mustache_Autoloader::register();

            $instance = new \Mustache_Engine([
                'template_class_prefix' => $config->templateClassPrefix,
                'cache'                 => $config->cacheDir,
                'loader'                => new \Mustache_Loader_FilesystemLoader($config->templateDir),
                /*
                'partials_loader'       => new \Mustache_Loader_CascadingLoader([
                    new FilesystemAliasLoader($config->templateDir),
                    new \Mustache_Loader_FilesystemLoader($config->templateDir),
                ]),
                */
                'partials_loader'       => new FilesystemAliasLoader($config->templateDir),
                'escape'                => function($value) {
                    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                },
                'charset'               => 'UTF-8',
                //'logger'                => null,
                'logger'                => new \Mustache_Logger_StreamLogger('php://stderr'),
            ]);

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}