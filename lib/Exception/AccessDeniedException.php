<?php

namespace Exception;

use Exception;

class AccessDeniedException extends \RuntimeException {

    /** @var string|null */
    private $redirectUrl;

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $url
     * @return mixed
     */
    public function setRedirectUrl($url)
    {
        $this->redirectUrl = (string)$url;
    }
}
