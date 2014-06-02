<?php

return function($debug) {
    ini_set('log_errors', true);
    //ini_set('error_log', $applicationDir . '/log/php-error.log');
    //ini_set('ignore_repeated_source', false);
    //ini_set('ignore_repeated_errors', true);

    if ($debug) {
        //ini_set('display_errors', true);
        ini_set('display_errors', false);
        error_reporting(-1);
    } else {
        ini_set('display_errors', false);
    }
};