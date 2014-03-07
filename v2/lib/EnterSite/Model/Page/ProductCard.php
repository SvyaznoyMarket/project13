<?php

namespace EnterSite\Model\Page {
    use EnterSite\Model\Page;

    class ProductCard extends Page\DefaultLayout {
        /** @var ProductCard\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new ProductCard\Content();
        }
    }
}

namespace EnterSite\Model\Page\ProductCard {
    use EnterSite\Model\Page;

    class Content extends Page\DefaultLayout\Content {
        /** @var Content\Product */
        public $product;

        public function __construct() {
            parent::__construct();

            $this->product = new Content\Product();
        }
    }
}

namespace EnterSite\Model\Page\ProductCard\Content {
    class Product {
        /** @var string */
        public $title;
        /** @var string */
        public $article;
        /** @var string */
        public $description;
        /** @var Product\Photo[] */
        public $photos = [];
        /** @var Product\PropertyChunk[] */
        public $propertyChunks = [];
        /** @var Product\Rating|null */
        public $rating;
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product {
    class Photo {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }

    class PropertyChunk {
        /** @var PropertyChunk\Property[] */
        public $properties = [];
    }

    class Rating {
        /** @var int */
        public $reviewCount;
        /** @var Rating\Star[] */
        public $stars = [];
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\PropertyChunk {
    class Property {
        /** @var string */
        public $name;
        /** @var string */
        public $value;
        /** @var bool */
        public $isTitle;
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\Rating {
    class Star {
        /** @var string */
        public $image;
    }
}