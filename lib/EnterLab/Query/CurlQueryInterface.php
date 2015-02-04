<?php

namespace EnterLab\Query;

use EnterLab\Curl;

interface CurlQueryInterface
{
    /**
     * @return Curl\Request
     */
    public function getRequest();
}