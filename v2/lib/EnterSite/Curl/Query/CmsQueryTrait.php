<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property Url $url
 * @property array $data
 * @property int $timeout
 * @property \Exception|null $error
 * @property string $response
 */
trait CmsQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->cmsService;

        $this->url->prefix = $config->url;
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
        } catch (\Exception $e) {
            $this->error = $e;
        }

        return $response;
    }
}