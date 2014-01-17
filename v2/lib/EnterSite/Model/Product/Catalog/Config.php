<?php

namespace EnterSite\Model\Product\Catalog {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterSite\Model\Product\Catalog\Config\BannerPlaceholder;

    class Config {
        use ImportArrayConstructorTrait;

        /** @var BannerPlaceholder */
        public $bannerPlaceholder;

        public function import(array $data) {
            if (array_key_exists('bannerPlaceholder', $data) && is_array($data['bannerPlaceholder'])) {
                $this->bannerPlaceholder = new BannerPlaceholder($data['bannerPlaceholder']);
            }
        }
    }
}

namespace EnterSite\Model\Product\Catalog\Config {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class BannerPlaceholder {
        use ImportArrayConstructorTrait;

        /** @var int */
        public $position;
        /** @var string */
        public $image;

        public function import(array $data) {
            if (array_key_exists('position', $data)) $this->position = (int)$data['position'];
            if (array_key_exists('image', $data)) $this->image = (string)$data['image'];
        }
    }
}
