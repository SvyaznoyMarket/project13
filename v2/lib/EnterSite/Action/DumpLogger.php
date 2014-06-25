<?php

namespace EnterSite\Action;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;

class DumpLogger {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    public function execute() {
        try {
            $this->getLogger()->dump();
        } catch (\Exception $e) {
            trigger_error($e, E_USER_ERROR);
        }
    }
}