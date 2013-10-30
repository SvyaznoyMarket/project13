<?php

namespace Enter\Site\Curl\Query;

use Enter\Site\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property string $url
 * @property array $data
 * @property string $auth
 * @property \Exception|null $error
 */
trait AdminQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->adminService;

        $this->url = $config->url . $this->url;
        if ($config->user && $config->password) {
            $this->auth = $config->user . ':' . $config->password;
        }
    }

    protected function parse($response) {
        try {
            $response = $this->jsonToArray($response);
        } catch (\Exception $e) {
            $this->error = $e;
            $response = [];
        }

        if (array_key_exists('error', $response)) {
            $response = array_merge(['code' => 0, 'message' => null], $response);
            $this->error = new \Exception($response['message'], $response['code']);
        }

        return (array)$response;
    }
}