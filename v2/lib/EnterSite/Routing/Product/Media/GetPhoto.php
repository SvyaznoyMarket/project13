<?php

namespace EnterSite\Routing\Product\Media;

use EnterSite\Routing\Route;
use EnterSite\ConfigTrait;
use EnterSite\Config;

class GetPhoto extends Route {
    use ConfigTrait;

    /** @var Config\Application */
    protected $config;

    /**
     * @param string $photoSource
     * @param string $photoId
     * @param string $photoSize
     */
    public function __construct($photoSource, $photoId, $photoSize) {
        $this->config = $this->getConfig();

        $this->parameters = [
            'photoId'     => $photoId,
            'photoSource' => $photoSource,
            'photoSize'   => $photoSize,
        ];
    }

    /**
     * @return string
     */
    public function __toString() {
        return
            $this->getHost((int)$this->parameters['photoId'])
            . (array_key_exists($this->parameters['photoSize'], $this->config->productPhoto->urlPaths) ? $this->config->productPhoto->urlPaths[$this->parameters['photoSize']] : reset($this->config->productPhoto->urlPaths))
            . $this->parameters['photoSource']
        ;
    }

    /**
     * @param int $photoId
     * @return string
     */
    protected function getHost($photoId) {
        $hosts = $this->config->mediaHosts;
        $index = !empty($photoId) ? ($photoId % count($hosts)) : rand(0, count($hosts) - 1);

        return isset($hosts[$index]) ? $hosts[$index] : '';
    }
}