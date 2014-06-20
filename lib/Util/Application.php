<?php

namespace Util;

class Application {

    /**
     * Возращает версию приложения.
     * Tag для production и time() для debug-окружения
     *
     * Для генерации версии необходим git post-checkout hook:
     * #!/bin/sh
     * git describe --tags > web/version.txt
     *
     * @return string
     */
    public static function getVersion() {

        if ( !\App::config()->debug ) {
            try {
                $version = $string = str_replace(PHP_EOL, '', file_get_contents(\App::config()->webDir . '/version.txt'));
            } catch (\Exception $e) {
                $version = (string)time();
                \App::logger()->error($e, ['version']);
            }
        } else {
            $version = (string)time();
        }

        return $version;
    }

} 