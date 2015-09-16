<?php
namespace Model\Product\Category {
    class Config {
        /** @var Config\ListingDefaultView */
        public $listingDefaultView;
        /** @var bool */
        public $listingDisplaySwitch = false;

        public function __construct($data = []) {
            if (isset($data['listing_default_view'])) {
                $this->listingDefaultView = new Config\ListingDefaultView($data['listing_default_view']);
            } else {
                $this->listingDefaultView = new Config\ListingDefaultView();
            }

            if (isset($data['listing_display_switch'])) $this->listingDisplaySwitch = (bool)$data['listing_display_switch'];
        }
    }
}

namespace Model\Product\Category\Config {
    class ListingDefaultView {
        /** @var bool */
        public $isList = false;
        /** @var bool */
        public $isMosaic = true;

        public function __construct($listingDefaultView = '') {
            if ($listingDefaultView === 'list') {
                $this->isList = true;
                $this->isMosaic = false;
            } else if ($listingDefaultView === 'mosaic') {
                $this->isList = false;
                $this->isMosaic = true;
            }
        }
    }
}