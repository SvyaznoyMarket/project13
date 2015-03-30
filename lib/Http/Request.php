<?php

namespace Http;

/**
 * Request represents an HTTP request.
 *
 * The methods dealing with URL accept / return a raw path (% encoded):
 *   * getBasePath
 *   * getBaseUrl
 *   * getPathInfo
 *   * getRequestUri
 *   * getUri
 *   * getUriForPath
 *
 * @api
 * @link http://symfony.com/doc/current/components/http_foundation/introduction.html
 */
class Request extends \Symfony\Component\HttpFoundation\Request {

    /**
     * @var string
     */
    protected $defaultLocale = 'ru';

}
