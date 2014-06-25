<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property Url $url
 * @property int $timeout
 * @property \Exception|null $error
 * @property string $response
 */
trait CoreQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->coreService;

        $this->url->prefix = $config->url;
        $this->url->query['client_id'] = $config->clientId;
        $this->timeout = $config->timeout;
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
            } else if (array_key_exists('result', $response)) {
                $response = $response['result'];
            }
        } catch (\Exception $e) {
            $this->error = $e;
        }

        return $response;
    }
}