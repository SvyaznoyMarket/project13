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
    use EnterSite\Model\Partial;

    class Product {
        /** @var string */
        public $name;
        /** @var string */
        public $namePrefix;
        /** @var string */
        public $article;
        /** @var int */
        public $price;
        /** @var string */
        public $shownPrice;
        /** @var int */
        public $oldPrice;
        /** @var string */
        public $shownOldPrice;
        /** @var Partial\ProductCard\CartButtonBlock|null */
        public $cartButtonBlock;
        /** @var Product\DeliveryBlock */
        public $deliveryBlock;
        /** @var string */
        public $description;
        /** @var Product\Photo[] */
        public $photos = [];
        /** @var bool */
        public $hasVideo;
        /** @var Product\Video[] */
        public $videos = [];
        /** @var bool */
        public $hasPhoto3d;
        /** @var Product\Photo3d[] */
        public $photo3ds = [];
        /** @var Product\PropertyChunk[] */
        public $propertyChunks = [];
        /** @var Partial\Rating|null */
        public $rating;
        /** @var Partial\ProductSlider|null */
        public $accessorySlider;
        /** @var Partial\ProductSlider|null */
        public $alsoBoughtSlider;
        /** @var Partial\ProductSlider|null */
        public $alsoViewedSlider;
        /** @var Partial\ProductSlider|null */
        public $similarSlider;
        /** @var Product\ReviewBlock|null */
        public $reviewBlock;
        /** @var bool */
        public $hasModel;
        /** @var Product\ModelBlock|null */
        public $modelBlock;
        /** @var Product\ModelBlock|null */
        public $moreModelBlock;
        /** @var Partial\DirectCredit|null */
        public $credit;

        public function __construct() {}
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product {
    use EnterSite\Model\Partial;

    class DeliveryBlock {
        /** @var DeliveryBlock\Delivery[] */
        public $deliveries = [];
    }

    class Photo {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }

    class Video {
        /** @var string */
        public $content;
    }

    class Photo3d {
        /** @var string */
        public $source;
    }

    class PropertyChunk {
        /** @var PropertyChunk\Property[] */
        public $properties = [];
    }

    class ReviewBlock {
        /** @var ReviewBlock\Review[] */
        public $reviews = [];
    }

    class ModelBlock {
        /** @var ModelBlock\Property[] */
        public $properties = [];
        /** @var bool */
        public $isImage;
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\DeliveryBlock {
    class Delivery {
        /** @var string */
        public $token;
        /** @var string */
        public $name;
        /** @var string */
        public $priceText;
        /** @var string */
        public $deliveredAtText;
        /** @var bool */
        public $hasShops;
        /** @var Delivery\Shop[] */
        public $shops = [];
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\DeliveryBlock\Delivery {
    class Shop {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
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

namespace EnterSite\Model\Page\ProductCard\Content\Product\ReviewBlock {
    use EnterSite\Model\Partial;

    class Review {
        /** @var string */
        public $createdAt;
        /** @var string */
        public $author;
        /** @var Partial\Rating\Star[] */
        public $stars = [];
        /** @var string */
        public $extract;
        /** @var string */
        public $pros;
        /** @var string */
        public $cons;
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\ModelBlock {
    class Property {
        /** @var string */
        public $name;
        /** @var bool */
        public $isImage;
        /** @var Property\Option[] */
        public $options = [];
    }
}

namespace EnterSite\Model\Page\ProductCard\Content\Product\ModelBlock\Property {
    class Option {
        /** @var string */
        public $shownValue;
        /** @var string */
        public $url;
        /** @var bool */
        public $isActive;
        /** @var string */
        public $image;
        /** @var string */
        public $unit;
    }
}