<?php

namespace EnterSite;

use Enter\Http;

trait SessionTrait {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @return Http\Session
     */
    protected function getSession() {
        if (!isset($GLOBALS[__METHOD__])) {
            $globalConfig = $this->getConfig();

            $config = new Http\Session\Config();
            $config->name = $globalConfig->session->name;
            $config->cookieLifetime = $globalConfig->session->cookieLifetime;
            $config->cookieDomain = $globalConfig->session->cookieDomain;

            $instance = new Http\Session($config);
            try {
                $instance->start();
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['critical', 'session']]);
            }

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}