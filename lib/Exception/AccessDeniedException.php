<?php

namespace Exception;

use Exception;

class AccessDeniedException extends \RuntimeException {

    /**
     * @param string $url
     */
    public function setRedirectUrl($url)
    {
        \App::session()->redirectUrl($url);
    }
}
