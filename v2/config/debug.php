<?php

return function() {
    $debug = false;

    if (isset($_GET['APPLICATION_DEBUG'])) {
        if (!empty($_GET['APPLICATION_DEBUG'])) {
            $debug = true;
        } else {
            $debug = false;
        }
    } else if (isset($_COOKIE['debug'])) {
        $debug = !empty($_COOKIE['debug']);
    }

    return $debug;
};