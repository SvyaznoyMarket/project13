<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();

            foreach ($product->getCategory() as $category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'url'  => $category->getLink(),
                );
            }
            $breadcrumbs[] = array(
                'name' => $product->getName(),
                'url'  => $product->getLink(),
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        // seo: page meta
        $this->setTitle(sprintf(
            '%s - купить по цене %s руб. в Москве, %s - характеристиками и описанием и фото от интернет-магазина Enter.ru',
            $product->getName(),
            $product->getPrice(),
            $product->getName()
        ));
        $this->addMeta('description', sprintf(
            'Интернет магазин Enter.ru предлагает купить: %s по цене %s руб. На нашем сайте Вы найдете подробное описание и характеристики товара %s с фото. Заказать понравившийся товар с доставкой по Москве можно у нас на сайте или по телефону ' . \App::config()->company['phone'] . '.',
            $product->getName(),
            $product->getPrice(),
            $product->getName()
        ));
        $this->addMeta('keywords', sprintf('%s Москва интернет магазин купить куплю заказать продажа цены', $product->getName()));
    }

    public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotMetaOg() {
        /** @var \Model\Product\Entity $product  */
        $product = $this->getParam('product');

        if (!$product) {
            return '';
        }

        if ($product->getDescription()) {
            $description = $product->getDescription();
        } elseif ($product->getTagline()) {
            $description = $product->getTagline();
        } else {
            $description = 'Enter - новый способ покупать. Любой из ' . number_format(\App::config()->product['totalCount'], 0, ',', ' ') . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
        }

        return "<meta property=\"og:title\" content=\"".$product->getName()."\"/>\r\n".
                "<meta property=\"og:description\" content=\"".$description."\"/>\r\n".
                "<meta property=\"og:image\" content=\"".$product->getImageUrl(3)."\"/>\r\n".
                "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
                "<meta property=\"og:type\" content=\"website\"/>\r\n";
    }

    public function slotAdvanceSeoCounter() {
        /** @var \Model\Product\Entity $product  */
        $product = $this->getParam('product');

        if (!$product) {
            return '';
        }

        return "<div id=\"heiasProduct\" data-vars=\"".$product->getId()."\" class=\"jsanalytics\"></div>\r\n".
                "<div id=\"marketgidProd\" class=\"jsanalytics\"></div>\r\n";
    }

    public function slotAdriver() {
        /** @var \Model\Product\Entity $product  */
        $product = $this->getParam('product');

        if (!$product) {
            $data = array(
                'productId' => 0,
                'categoryId' => 0,
            );
        }
        else {
            $data = array(
                'productId' => $product->getId(),
                'categoryId' => 0,
            );
        }

        return "<div id=\"adriverCommon\" data-vars='".json_encode( $data )."' class=\"jsanalytics\"></div>\r\n";
    }
}
