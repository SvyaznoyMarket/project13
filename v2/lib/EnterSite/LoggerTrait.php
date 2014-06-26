<?php

namespace EnterSite;

use Enter\Logging;

trait LoggerTrait {
    use ConfigTrait;

    /**
     * @return Logging\Logger
     */
    protected function getLogger() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = $this->getConfig()->logger;

            $appenders = [];
            if ($config->fileAppender->enabled) {
                $appenders[] = new Logging\FileAppender($config->fileAppender->file);
            }

            $GLOBALS[__METHOD__] = new Logging\Logger($appenders, null, [
                '_id' => $this->getConfig()->requestId,
            ]);
        }

        return $GLOBALS[__METHOD__];
    }
}