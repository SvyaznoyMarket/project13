<?php

namespace EnterSite\Routing\Product\Category;

use EnterSite\Routing\Route;
use EnterSite\ConfigTrait;
use EnterSite\Config;

class GetImage extends Route {
    use ConfigTrait;

    /** @var Config\Application */
    protected $config;

    /**
     * @param string $imageSource
     * @param string $categoryId
     * @param string $imageSize
     */
    public function __construct($imageSource, $categoryId, $imageSize) {
        $this->config = $this->getConfig();

        $this->parameters = [
            'categoryId'  => $categoryId,
            'imageSource' => $imageSource,
            'imageSize'   => $imageSize,
        ];
    }

    /**
     * @return string
     */
    public function __toString() {
        return
            $this->getHost((int)$this->parameters['categoryId'])
            . (array_key_exists($this->parameters['imageSize'], $this->config->productCategoryPhoto->urlPaths) ? $this->config->productCategoryPhoto->urlPaths[$this->parameters['imageSize']] : reset($this->config->productCategoryPhoto->urlPaths))
            . $this->parameters['imageSource']
        ;
    }

    /**
     * @param int $categoryId
     * @return string
     */
    protected function getHost($categoryId) {
        $hosts = $this->config->mediaHosts;
        $index = !empty($categoryId) ? ($categoryId % count($hosts)) : rand(0, count($hosts) - 1);

        return isset($hosts[$index]) ? $hosts[$index] : '';
    }
}