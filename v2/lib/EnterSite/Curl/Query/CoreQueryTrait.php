<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property string $url
 * @property int $timeout
 * @property \Exception|null $error
 */
trait CoreQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->coreService;

        $this->url = $config->url . $this->url;
        $this->timeout = $config->timeout;
    }

    /**
     * @param $response
     * @return array
     */
    protected function parse($response) {
        try {
            $response = $this->jsonToArray($response);
        } catch (\Exception $e) {
            $this->error = $e;
        }
        $response = (array)$response;

        if (array_key_exists('error', $response)) {
            $response = array_merge(['code' => 0, 'message' => null], $response['error']);
            $this->error = new \Exception($response['message'], $response['code']);
        } else if (array_key_exists('result', $response)) {
            $response = $response['result'];
        }

        return $response;
    }
}