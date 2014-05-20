<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property Url $url
 * @property array $data
 * @property int $timeout
 * @property string $auth
 * @property \Exception|null $error
 * @property string $response
 */
trait ReviewQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->reviewService;

        $this->url->prefix = $config->url;
        $this->timeout = $config->timeout;
        if ($config->user && $config->password) {
            $this->auth = $config->user . ':' . $config->password;
        }
    }

    /**
     * @param $response
     * @return array
     */
    protected function parse($response) {
        if ($this->getConfig()->curl->logResponse) {
            $this->response = $response;
        }

        try {
            $response = $this->jsonToArray($response);
            if (array_key_exists('error', $response)) {
                $response = array_merge(['code' => 0, 'message' => null], $response['error']);

                throw new \Exception($response['message'], $response['code']);
            }
        } catch (\Exception $e) {
            $this->error = $e;
        }

        return $response;
    }
}