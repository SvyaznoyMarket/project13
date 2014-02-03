<?php

namespace EnterSite\Action;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;

class DumpLogger {
    use ConfigTrait;
    use LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    public function execute() {
        $this->getLogger()->dump();
    }
}