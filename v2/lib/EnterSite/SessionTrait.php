<?php

namespace EnterSite;

use Enter\Http;

trait SessionTrait {
    use ConfigTrait;
    use LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @return Http\Session
     */
    protected function getSession() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Http\Session\Config();
            $config->name = $this->getConfig()->session->name;
            $config->cookieLifetime = $this->getConfig()->session->cookieLifetime;

            $instance = new Http\Session($config);
            try {
                $instance->start();
            } catch (\Exception $e) {
                $this->getLogger()->push(['type' => 'critical', 'error' => $e, 'action' => __METHOD__, 'tag' => ['session']]);
            }

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}