<?php

namespace Enter\Http;

class JsonResponse extends Response {
    /** @var \ArrayObject */
    public $data;

    public function __construct($data = null, $statusCode = self::STATUS_OK) {
        parent::__construct(null, $statusCode);

        $this->data = new \ArrayObject($data ?: []);

        $this->headers['Content-Type'] = 'application/json';
    }

    /**
     * @return $this
     */
    public function sendContent() {
        $this->content = json_encode($this->data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);

        return parent::sendContent();
    }
}