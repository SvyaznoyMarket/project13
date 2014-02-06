<?php

namespace EnterSite\Curl\Query;

use EnterSite\ConfigTrait;
use Enter\Util\JsonDecoderTrait;

/**
 * @property Url $url
 * @property array $data
 * @property int $timeout
 * @property \Exception|null $error
 */
trait CmsQueryTrait {
    use JsonDecoderTrait;
    use ConfigTrait;

    protected function init() {
        $config = $this->getConfig()->cmsService;

        $this->url->prefix = $config->url;
        $this->timeout = $config->timeout;
    }

    protected function parse($response) {
        try {
            $response = $this->jsonToArray($response);
        } catch (\Exception $e) {
            $this->error = $e;
        }

        return $response;
    }
}