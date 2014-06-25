<?php

namespace EnterSite\Action;

use EnterTerminal\ConfigTrait;

class LoadConfig {
    use ConfigTrait;

    public function execute($configHandler) {
        $config = $this->getConfig();
        if (!is_callable($configHandler)) {
            throw new \Exception('Неправильный обработчик настроек');
        }
        call_user_func_array($configHandler, [$config]);
    }
}