<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model\Page;
    use EnterSite\Model\Page\ProductCatalog\ChildCategory\ProductCard;
    use EnterSite\Model\Region;
    use EnterSite\Model\Product\Category;
    use EnterSite\Model\Product;

    class ChildCategory extends Page\DefaultLayout {
        /** @var Page\ProductCatalog\ChildCategory\ProductCard[] */
        public $productCards = [];

        public function __construct(
            Region $region,
            Category $category,
            array $products
        ) {
            $this->title = $category->name . ' - Enter';
            $this->header = $category->name;

            $this->setRegionLink($region);
            $this->setProductCards($products);
        }

        /**
         * @param Product[] $products
         */
        protected function setProductCards(array $products) {
            foreach ($products as $product) {
                $productCard = new ProductCard();
                $productCard->name = $product->name;
                $productCard->url = $product->link;

                $this->productCards[] = $productCard;
            }
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory {
    class ProductCard {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}