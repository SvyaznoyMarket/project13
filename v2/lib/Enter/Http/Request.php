<?php

namespace Enter\Http;

class Request {
    /**
     * The GET parameters
     * @var Bag
     */
    public $query;
    /**
     * The POST parameters
     * @var Bag
     */
    public $data;
    /**
     * The COOKIE parameters
     * @var Bag
     */
    public $cookies;
    /**
     * The FILES parameters
     * @var FileBag
     */
    public $files;
    /**
     * The SERVER parameters
     * @var Bag
     */
    public $server;
    /**
     * php://input string
     * @var string
     */
    protected $content;
    /**
     * php://input resource
     * @var resource
     */
    protected $contentResource;
    /** @var string */
    protected $baseUrl;
    /** @var string */
    protected $pathInfo;
    /** @var string */
    protected $requestUri;
    /** @var string */
    protected $method;

    public function __construct($query = [], $data = [], $cookie = [], $file = [], $server = []) {
        $this->query = new Bag($query);
        $this->data = new Bag($data);
        $this->cookies = new Bag($cookie);
        $this->files = new FileBag($file);
        $this->server = new Bag($server);
    }

    public function __clone() {
        $this->query = clone $this->query;
        $this->data = clone $this->data;
        $this->cookies = clone $this->cookies;
        $this->files = clone $this->files;
        $this->server = clone $this->server;
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest() {
        return 'XMLHttpRequest' == $this->server['HTTP_X_REQUESTED_WITH'];
    }

    /**
     * @return string
     */
    public function getHost() {
        if ($host = $this->server['HTTP_X_FORWARDED_HOST']) {
            $elements = explode(',', $host);

            $host = $elements[count($elements) - 1];
        } elseif (!$host = $this->server['HTTP_HOST']) {
            if (!$host = $this->server['SERVER_NAME']) {
                $host = $this->server['SERVER_ADDR'];
            }
        }

        return $host;
    }

    /**
     * @return int
     */
    public function getPort() {
        if ($port = $this->server['HTTP_X_FORWARDED_PORT']) {
            return $port;
        }

        if ('https' === $this->server['HTTP_X_FORWARDED_PROTO']) {
            return 443;
        }

        if ($host = $this->server['HTTP_HOST']) {
            if (false !== $pos = strrpos($host, ':')) {
                return intval(substr($host, $pos + 1));
            }

            return 'https' === $this->getScheme() ? 443 : 80;
        }

        return $this->server['SERVER_PORT'];
    }

    /**
     * @return bool
     */
    public function isSecure() {
        return 'on' == strtolower($this->server['HTTPS']) || 1 == $this->server['HTTPS'];
    }

    /**
     * @return string
     */
    public function getScheme() {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * @return string
     */
    public function getHttpHost() {
        $scheme = $this->getScheme();
        $port   = $this->getPort();

        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }

        return $this->getHost() . ':' . $port;
    }

    /**
     * @return string
     */
    public function getSchemeAndHttpHost() {
        return $this->getScheme() . '://' . $this->getHttpHost();
    }

    public function getRequestUri() {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }

        return $this->requestUri;
    }

    public function getPathInfo() {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * @return string
     */
    public function getUri() {
        if (null !== $queryString = $this->getQueryString()) {
            $queryString = '?' . $queryString;
        }

        return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $this->getPathInfo() . $queryString;
    }

    /**
     * @return string
     */
    public function getBaseUrl() {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }

        return $this->baseUrl;
    }

    /**
     * @return string|null
     */
    public function getQueryString() {
        $queryString = $this->normalizeQueryString($this->server['QUERY_STRING']);

        return '' === $queryString ? null : $queryString;
    }

    /**
     * @return string
     */
    public function getClientIp() {
        return $this->server['HTTP_X_FORWARDED_FOR'] ?: $this->server['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public function getScriptName() {
        return $this->server['SCRIPT_NAME'] ?: $this->server['ORIG_SCRIPT_NAME'];
    }

    /**
     * @return string
     */
    public function getMethod() {
        if (null === $this->method) {
            $this->method = strtoupper($this->server['REQUEST_METHOD']);

            if ('POST' === $this->method) {
                if ($method = $this->server['HTTP_X_HTTP_METHOD_OVERRIDE']) {
                    $this->method = strtoupper($method);
                }
            }
        }

        return $this->method;
    }

    /**
     * @return string
     */
    public function getContent() {
        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * @return resource
     */
    public function getContentResource() {
        if (null === $this->contentResource) {
            $this->contentResource = fopen('php://input', 'rb');
        }

        return $this->contentResource;
    }

    /**
     * @return string
     */
    private function preparePathInfo() {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        $pathInfo = '/';

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if (null !== $baseUrl && false === $pathInfo = substr($requestUri, strlen($baseUrl))) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } else if (null === $baseUrl) {
            return $requestUri;
        }

        return (string)$pathInfo;
    }

    /**
     * @return string
     */
    private function prepareBaseUrl() {
        $filename = basename($this->server['SCRIPT_FILENAME']);

        if (basename($this->server['SCRIPT_NAME']) === $filename) {
            $baseUrl = $this->server['SCRIPT_NAME'];
        } elseif (basename($this->server['PHP_SELF']) === $filename) {
            $baseUrl = $this->server['PHP_SELF'];
        } elseif (basename($this->server['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $this->server['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $this->server['PHP_SELF'];
            $file = $this->server['SCRIPT_FILENAME'];
            $segments = explode('/', trim($file, '/'));
            $segments = array_reverse($segments);
            $index = 0;
            $last = count($segments);
            $baseUrl = '';
            do {
                $segment     = $segments[$index];
                $baseUrl = '/' . $segment . $baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        // Does the baseUrl have anything in common with the request_uri?
        $requestUri = $this->getRequestUri();

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }

        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, dirname($baseUrl))) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/');
        }

        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    /**
     * @return string
     */
    private function prepareRequestUri() {
        $requestUri = '';

        if ($this->server['REQUEST_URI']) {
            $requestUri = $this->server['REQUEST_URI'];
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (0 === strpos($requestUri, $schemeAndHttpHost)) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } else if ($this->server['ORIG_PATH_INFO']) {
            // PHP as CGI
            $requestUri = $this->server['ORIG_PATH_INFO'];
            if ('' != $this->server['QUERY_STRING']) {
                $requestUri .= '?' . $this->server['QUERY_STRING'];
            }
            unset($this->server['ORIG_PATH_INFO']);
        }

        return $requestUri;
    }

    /**
     * @param $queryString
     * @return string
     */
    private function normalizeQueryString($queryString) {
        if ('' == $queryString) {
            return '';
        }

        $parts = [];
        $order = [];

        foreach (explode('&', $queryString) as $param) {
            if ('' === $param || '=' === $param[0]) {
                // Ignore useless delimiters, e.g. "x=y&".
                // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
                // PHP also does not include them when building _GET.
                continue;
            }

            $keyValuePair = explode('=', $param, 2);

            // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
            // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
            // RFC 3986 with rawurlencode.
            $parts[] = isset($keyValuePair[1]) ?
                rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) :
                rawurlencode(urldecode($keyValuePair[0]));
            $order[] = urldecode($keyValuePair[0]);
        }

        array_multisort($order, SORT_ASC, $parts);

        return implode('&', $parts);
    }

    /**
     * @param $string
     * @param $prefix
     * @return bool|string
     */
    private function getUrlencodedPrefix($string, $prefix) {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }

        $len = strlen($prefix);

        if (preg_match("#^(%[[:xdigit:]]{2}|.){{$len}}#", $string, $match)) {
            return $match[0];
        }

        return false;
    }
}