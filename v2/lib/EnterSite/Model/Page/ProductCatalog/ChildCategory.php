<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model;

    class ChildCategory extends Model\Page\DefaultLayout {
        /** @var ChildCategory\Content */
        public $content;

        public function __construct(
            Model\Region $region,
            Model\Product\Category $category,
            Model\Product\Catalog\Config $catalogConfig,
            array $products
        ) {
            $this->title = $category->name . ' - Enter';
            $this->header = $category->name;

            $this->setRegionLink($region);
            $this->setContent(
                $products,
                $catalogConfig
            );
        }

        /**
         * @param Model\Product[] $products
         * @param Model\Product\Catalog\Config $catalogConfig
         */
        protected function setContent(
            array $products,
            Model\Product\Catalog\Config $catalogConfig
        ) {
            $this->content = new ChildCategory\Content();
            $this->content->catalogConfig = $catalogConfig;

            foreach ($products as $product) {
                $productCard = new ChildCategory\Content\ProductBlock\ProductCard();
                $productCard->name = $product->name;
                $productCard->url = $product->link;

                $this->content->productBlock->products[] = $productCard;
            }
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory {
    use EnterSite\Model;

    class Content {
        /** @var string */
        public $title;
        /** @var Content\ProductBlock */
        public $productBlock;
        /** @var Model\Product\Catalog\Config */
        public $catalogConfig;

        public function __construct() {
            $this->productBlock = new Content\ProductBlock();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content {
    class ProductBlock {
        /** @var ProductBlock\ProductCard[] */
        public $products = [];
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content\ProductBlock {
    class ProductCard {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}