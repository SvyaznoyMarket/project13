<?php
namespace Model\Product\Category {
    class Config
    {
        /** @var Config\ListingDefaultView */
        public $listingDefaultView;
        /** @var bool */
        public $listingDisplaySwitch = false;
        /** @var string|null */
        public $categoryView;

        public function __construct($data = [])
        {
            if (isset($data['listing_default_view'])) {
                $this->listingDefaultView = new Config\ListingDefaultView($data['listing_default_view']);
            } else {
                $this->listingDefaultView = new Config\ListingDefaultView();
            }

            if (isset($data['listing_display_switch'])) {
                $this->listingDisplaySwitch = (bool)$data['listing_display_switch'];
            }

            if (array_key_exists('category_view', $data)) {
                $this->categoryView = $data['category_view'];
            }
        }

        public function isListingView()
        {
            return $this->categoryView === 'default';
        }

        public function isManualGridView()
        {
            return $this->categoryView === 'grid_manual';
        }

        public function isAutoGridView()
        {
            return $this->categoryView === 'grid_auto';
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