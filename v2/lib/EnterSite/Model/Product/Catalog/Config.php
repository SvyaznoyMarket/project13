<?php

namespace EnterSite\Model\Product\Catalog {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class Config {
        use ImportArrayConstructorTrait;

        /** @var Config\BannerPlaceholder */
        public $bannerPlaceholder;
        /** @var Config\PromoStyle */
        public $promoStyle;
        /** @var string */
        public $listingStyle;
        /** @var array */
        public $accessoryCategoryTokens = [];
        /** @var Config\Sorting[] */
        public $sortings = [];

        public function import(array $data) {
            if (isset($data['bannerPlaceholder']) && is_array($data['bannerPlaceholder'])) $this->bannerPlaceholder = new Config\BannerPlaceholder($data['bannerPlaceholder']);
            if (isset($data['promo_style']) && is_array($data['promo_style'])) $this->promoStyle = new Config\PromoStyle($data['promo_style']);
            if (isset($data['listing_style'])) $this->listingStyle = (string)$data['listing_style'];
            if (isset($data['accessory_category_token'][0])) {
                foreach (array_unique($data['accessory_category_token']) as $accessoryCategoryToken) {
                    if (!is_scalar($accessoryCategoryToken)) continue;
                    $this->accessoryCategoryTokens[] = trim((string)$accessoryCategoryToken);
                }
            }
            if (isset($data['sort']) && is_array($data['sort'])) {
                foreach ($data['sort'] as $sortingName => $sortingItem) {
                    if (!$sortingName) continue;

                    $this->sortings[$sortingName] = new Config\Sorting($sortingItem);
                }
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
            if (isset($data['position'])) $this->position = (int)$data['position'];
            if (isset($data['image'])) $this->image = (string)$data['image'];
        }
    }

    class PromoStyle {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $bCustomFilter;
        /** @var string */
        public $bTitlePage;
        /** @var string */
        public $bFilterHead;
        /** @var string */
        public $bPopularSection;
        /** @var string */
        public $bCatalogList;
        /** @var string */
        public $bCatalogList__eItem;
        /** @var string */
        public $bRangeSlider;

        public function import(array $data) {
            if (isset($data['promo_image'])) $this->bCustomFilter = (string)$data['promo_image'];
            if (isset($data['bCustomFilter'])) $this->bCustomFilter = (string)$data['bCustomFilter'];
            if (isset($data['title'])) $this->bTitlePage = (string)$data['title'];
            if (isset($data['bTitlePage'])) $this->bTitlePage = (string)$data['bTitlePage'];
            if (isset($data['bFilterHead'])) $this->bFilterHead = (string)$data['bFilterHead'];
            if (isset($data['bPopularSection'])) $this->bPopularSection = (string)$data['bPopularSection'];
            if (isset($data['bCatalogList'])) $this->bCatalogList = (string)$data['bCatalogList'];
            if (isset($data['bCatalogList__eItem'])) $this->bCatalogList__eItem = (string)$data['bCatalogList__eItem'];
            if (isset($data['bRangeSlider'])) $this->bRangeSlider = (string)$data['bRangeSlider'];
        }
    }

    class Sorting {
        use ImportArrayConstructorTrait;

        /** @var int */
        public $statisticView;
        /** @var int */
        public $statisticPurchase;
        /** @var array */
        public $added = [];
        /** @var int */
        public $label;
        /** @var int */
        public $score;
        /** @var int */
        public $marginality;
        /** @var int */
        public $stock;
        /** @var int */
        public $conversion;
        /** @var int */
        public $is_store;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (isset($data['statisticView'])) $this->statisticView = (int)$data['statisticView'];
            if (isset($data['statisticPurchase'])) $this->statisticPurchase = (int)$data['statisticPurchase'];
            if (isset($data['added']) && is_array($data['added'])) $this->added = $data['added'];
            if (isset($data['label'])) $this->label = (int)$data['label'];
            if (isset($data['score'])) $this->score = (int)$data['score'];
            if (isset($data['marginality'])) $this->marginality = (int)$data['marginality'];
            if (isset($data['stock'])) $this->stock = (int)$data['stock'];
            if (isset($data['conversion'])) $this->conversion = (int)$data['conversion'];
            if (isset($data['is_store'])) $this->is_store = (int)$data['is_store'];
        }
    }
}
