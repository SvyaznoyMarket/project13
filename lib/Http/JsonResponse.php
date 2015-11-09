<?php

namespace Http;

/**
 * Response represents an HTTP response in JSON format.
 */
class JsonResponse extends Response {
    /** @var int|null */
    public static $jsonOptions = null;
    /** @var array */
    protected $data;
    protected $callback;

    /**
     * @param mixed   $data    The response data
     * @param integer $status  The response status code
     * @param array   $headers An array of response headers
     */
    public function __construct($data = [], $status = 200, $headers = []) {
        parent::__construct('', $status, $headers);

        $this->setData($data);
    }

    public static function create($data = [], $status = 200, $headers = []) {
        return new static($data, $status, $headers);
    }

    /**
     * Sets the JSONP callback.
     *
     * @param string $callback
     *
     * @throws \InvalidArgumentException
     * @return JsonResponse
     */
    public function setCallback($callback = null) {
        if (null !== $callback) {
            // taken from http://www.geekality.net/2011/08/03/valid-javascript-identifier/
            $pattern = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';
            $parts = explode('.', $callback);
            foreach ($parts as $part) {
                if (!preg_match($pattern, $part)) {
                    throw new \InvalidArgumentException('The callback name is not valid.');
                }
            }
        }

        $this->callback = $callback;

        return $this->update();
    }

    /**
     * Sets the data to be sent as json.
     *
     * @param mixed $data
     *
     * @return JsonResponse
     */
    public function setData($data = []) {
        // root should be JSON object, not array
        if (is_array($data) && 0 === count($data)) {
            $data = new \ArrayObject();
        }

        // Encode <, >, ', &, and " for RFC4627-compliant JSON, which may also be embedded into HTML.
        $this->data = $data;

        return $this->update();
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Updates the content and headers according to the json data and callback.
     *
     * @return JsonResponse
     */
    protected function update() {
        if (null !== $this->callback) {
            // Not using application/javascript for compatibility reasons with older browsers.
            $this->headers->set('Content-Type', 'text/javascript', true);

            return $this->setContent(sprintf('%s(%s);', $this->callback, $this->data));
        }

        $this->headers->set('Content-Type', 'application/json', false);

        $jsonOptions = (null === self::$jsonOptions) ? (JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) : self::$jsonOptions;

        return $this->setContent(json_encode($this->data, $jsonOptions));
    }
}
