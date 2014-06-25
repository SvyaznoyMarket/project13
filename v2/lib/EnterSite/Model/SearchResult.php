<?php

namespace EnterSite\Model {
    use EnterSite\Model\ImportArrayConstructorTrait;
    use EnterSite\Model;

    class SearchResult {
        use ImportArrayConstructorTrait;

        /** @var array */
        public $productIds = [];
        /** @var int */
        public $productCount;
        /** @var bool */
        public $isForcedMean;
        /** @var string */
        public $forcedMean;
        /** @var Model\SearchResult\Category[] */
        public $categories = [];

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('forced_mean', $data)) $this->isForcedMean = (bool)$data['forced_mean'];
            if (array_key_exists('did_you_mean', $data)) $this->forcedMean = $data['did_you_mean'] ? (string)$data['did_you_mean'] : null;

            $productData = isset($data['1']) ? (array)$data['1'] : [];

            if (array_key_exists('data', $productData)) $this->productIds = (array)$productData['data'];
            if (array_key_exists('count', $productData)) $this->productCount = (int)$productData['count'];
            if (isset($productData['category_list'][0])) {
                foreach ($productData['category_list'] as $categoryItem) {
                    if (empty($categoryItem['category_id'])) continue;

                    $this->categories[] = new SearchResult\Category($categoryItem);
                }
            }
        }
    }
}

namespace EnterSite\Model\SearchResult {
    use EnterSite\Model\ImportArrayConstructorTrait;

    class Category {
        use ImportArrayConstructorTrait;

        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $image;
        /** @var int */
        public $productCount;

        /**
         * @param array $data
         */
        public function import(array $data) {
            if (array_key_exists('category_id', $data)) $this->id = (string)$data['category_id'];
            if (array_key_exists('category_name', $data)) $this->name = (string)$data['category_name'];
            if (array_key_exists('category_image', $data)) $this->image = (string)$data['category_image'];
            if (array_key_exists('count', $data)) $this->productCount = (int)$data['count'];
        }
    }
}