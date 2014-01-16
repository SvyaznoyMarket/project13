<?php

namespace EnterSite\Model\Product\Catalog {
    use EnterSite\Model\ImportInterface;
    use EnterSite\Model\ImportConstructorTrait;
    use EnterSite\Model\Product\Catalog\Config\BannerPlaceholder;

    class Config implements ImportInterface {
        use ImportConstructorTrait;

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
    use EnterSite\Model\ImportInterface;
    use EnterSite\Model\ImportConstructorTrait;

    class BannerPlaceholder implements ImportInterface {
        use ImportConstructorTrait;

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
