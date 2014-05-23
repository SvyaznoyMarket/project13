<?php

return function() {
    $debug = false;

    if (isset($_GET['debug'])) {
        $debug = !empty($_GET['debug']);
    }

    return $debug;
};