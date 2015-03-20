<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';
    /** @var \Model\Product\Entity|null */
    protected $product;

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }
        $this->product = $product;

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];

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

        $page = new \Model\Page\Entity();

        try {
            $this->applySeoPattern($page);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        if (!$page->getTitle()) {
            $page->setTitle(sprintf(
                '%s - купить по цене %s руб. в Москве, %s - характеристиками и описанием и фото от интернет-магазина Enter.ru',
                $product->getName(),
                $product->getPrice(),
                $product->getName()
            ));
        }

        if (!$page->getDescription()) {
            $page->setDescription(sprintf(
                'Интернет магазин Enter.ru предлагает купить: %s по цене %s руб. На нашем сайте Вы найдете подробное описание и характеристики товара %s с фото. Заказать понравившийся товар с доставкой по Москве можно у нас на сайте или по телефону ' . \App::config()->company['phone'] . '.',
                $product->getName(),
                $product->getPrice(),
                $product->getName()
            ));
        }

        if (!$page->getKeywords()) {
            $page->setKeywords(sprintf('%s Москва интернет магазин купить куплю заказать продажа цены', $product->getName()));
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    private function applySeoPattern(\Model\Page\Entity $page) {
        $product = $this->getParam('product');
        if (!($product instanceof \Model\Product\Entity)) {
            return;
        }

        $replacer = new \Util\InflectReplacer([
            'цена' => $product->getPrice() . ' руб',
        ]);

        $page->setTitle($replacer->get($product->getSeoTitle()));
        $page->setDescription($replacer->get($product->getSeoDescription()));
        $page->setKeywords($replacer->get($product->getSeoKeywords()));
    }

    public function slotContentHead() {
        return $this->render('product/_contentHead', $this->params);
    }

    public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . $this->hasParam('categoryClass') ? ' ' . $this->getParam('categoryClass') : '';
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        /** @var $product \Model\Product\Entity */
        $product =  $this->getParam('product');
        if (!$product instanceof \Model\Product\Entity) return null;
        $categories = $product->getCategory();
        $category = array_pop($categories);

        $tag_params = [
            'prodid' => $product->getId(),
            'pagetype' => 'product',
            'pname' => $product->getName(),
            'pcat' => ($category) ? $category->getToken() : '',
            'pcat_upper' => $product->getMainCategory() ? $product->getMainCategory()->getToken() : '',
            'pvalue' => $product->getPrice()
        ];

        return parent::slotGoogleRemarketingJS($tag_params);
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

        return "<meta property=\"og:title\" content=\"" . $this->escape($product->getName()) . "\"/>\r\n" .
            "<meta property=\"og:description\" content=\"" . $this->escape($description) . "\"/>\r\n" .
            "<meta property=\"og:image\" content=\"" . $this->escape($product->getImageUrl(3).'?'.time()) . "\"/>\r\n".
            "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
            "<meta property=\"og:type\" content=\"website\"/>\r\n".
            "<link rel=\"image_src\" href=\"". $this->escape($product->getImageUrl(3)). "\" />\r\n";
    }

    public function slotConfig() {
        $config = ['location' => ['product']];

        /** @var \Model\Product\Entity|null $product */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if ($product) {
            $config['product'] = [
                'id' => $product->getId(),
                'isSlot' => (bool)$product->getSlotPartnerOffer(),
                'isOnlyFromPartner' => $product->isOnlyFromPartner(),
            ];
        }

        return $this->tryRender('_config', ['config' => $config]);
    }

    public function slotUserbarContent() {
        return $this->render('product/_userbarContent', [
            'product'   => $this->getParam('product') ? $this->getParam('product') : null,
        ]);
    }

    public function slotUserbarContentData() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }

        return [
            'target' => '.js-showTopBar',
            'productId' => $product->getId(),
        ];
    }

    public function slotAdvMakerJS() {
        if (!\App::config()->partners['AdvMaker']['enabled'] || empty($this->product)) return '';
        $product = [
            'id'        => $this->product->getId(),
            'vendor'    => $this->product->getBrand(),
            'price'     => $this->product->getPrice(),
            'url'       => \App::router()->generate('product', ['productPath' => $this->product->getToken()], true),
            'picture'   => $this->product->getImageUrl(),
            'name'      => $this->product->getName(),
            'category'  => $this->product->getLastCategory() ? $this->product->getLastCategory()->getId() : null
        ];
        return '<!-- AdvMaker -->
            <script type="text/javascript" defer="defer">
                $(window).load(function() {
                    window.advm_product = '. $this->json($product, false) .';
                    window.advm_ret = window.advm_ret || [];
                    window.advm_ret.push({code: "543e17ea03935", level: 3});
                    (function () {
                        var sc = document.createElement("script");
                        sc.async = true;
                        sc.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//rt.am15.net/retag/core/retag.js";
                        var tn = document.getElementsByTagName("script")[0];
                        tn.parentNode.insertBefore(sc, tn);
                    })()
                });
            </script>';
    }

    public function slotHubrusJS() {
        $html = parent::slotHubrusJS();
        if (!empty($html)) {
            return $html . \View\Partners\Hubrus::addProductData($this->product);
        }
    }

    public function slotGoogleAnalytics()
    {
        return $this->tryRender('_googleAnalytics', ['product' => $this->getParam('product')]);
    }

    public function slotGetIntentJS() {
        if (!\App::config()->partners['GetIntent']['enabled']) {
            return '';
        }

        /** @var \Model\Product\Entity|null $product */
        $product = $this->getParam('product');
        if (!($product instanceof \Model\Product\Entity)) {
            $product = null;
        }

        $data = [
            'productId' => $product ? (string)$product->getId() : '',
            'productPrice' => $product ? (string)$product->getPrice() : '',
            'categoryId' => $product && $product->getLastCategory() ? (string)$product->getLastCategory()->getId() : '',
        ];

        return '<div id="GetIntentJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }
}
