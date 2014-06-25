<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property RetailRocketUrl $url
 * @property array $data
 * @property int $timeout
 * @property string $auth
 * @property \Exception|null $error
 * @property string $response
 */
trait RetailRocketQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->retailRocketService;

        $this->url->prefix = $config->url;
        $this->url->account = $config->account;
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
            // TODO: обработка ошибок
        } catch (\Exception $e) {
            $this->error = $e;
        }

        return $response;
    }
}