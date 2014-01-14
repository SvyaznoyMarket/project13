<?php

namespace Enter\Site\Model\Product\Catalog {
    use Enter\Site\Model\ImportInterface;
    use Enter\Site\Model\ImportConstructorTrait;
    use Enter\Site\Model\Product\Catalog\Config\BannerPlaceholder;

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

namespace Enter\Site\Model\Product\Catalog\Config {
    use Enter\Site\Model\ImportInterface;
    use Enter\Site\Model\ImportConstructorTrait;

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
