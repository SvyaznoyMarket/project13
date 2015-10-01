<?php

namespace Model\Shop\Photo;

class Entity {
    /** @var string[] */
    private $urlsByType = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (isset($data['sources'][0])) {
            foreach ($data['sources'] as $sourceItem) {
                if (
                    !@$sourceItem['url']
                    || !@$sourceItem['type']
                ) continue;

                $this->urlsByType[(string)$sourceItem['type']] = (string)$sourceItem['url'];
            }

        }
    }

    /**
     * @param int $size
     * @return null|string
     */
    public function getUrl($size = null) {
        if (null === $size) {
            $size = key($this->urlsByType);
        }

        return isset($this->urlsByType[$size]) ? $this->urlsByType[$size] : null;
    }
}