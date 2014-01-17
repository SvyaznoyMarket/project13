<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model\Page;
    use EnterSite\Model\Region;
    use EnterSite\Model\Product;

    class ChildCategory extends Page\DefaultLayout {
        /** @var ChildCategory\Content */
        public $content;

        public function __construct(
            Region $region,
            Product\Category $category,
            array $products
        ) {
            $this->title = $category->name . ' - Enter';
            $this->header = $category->name;

            $this->setRegionLink($region);
            $this->setContent(
                $products
            );
        }

        /**
         * @param Product[] $products
         */
        protected function setContent(array $products) {
            $this->content = new ChildCategory\Content();

            foreach ($products as $product) {
                $productCard = new ChildCategory\Content\ProductCard();
                $productCard->name = $product->name;
                $productCard->url = $product->link;

                $this->content->productCards[] = $productCard;
            }
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory {
    class Content {
        /** @var Content\ProductCard[] */
        public $productCards = [];
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content {
    class ProductCard {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}