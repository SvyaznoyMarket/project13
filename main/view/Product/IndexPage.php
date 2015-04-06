<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';
    /** @var \Model\Product\Entity|null */
    protected $product;
    /** Карточка товара 2015
     * @var bool
     */
    protected $isNewProductPage = true;

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) return;
        $this->product = $product;

        // Хлебные крошки
        $this->prepareBreadcrumbs();

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

        $replacer = new \Util\InflectReplacer([
            'цена' => $this->product->getPrice() . ' руб',
        ]);

        $page->setTitle($replacer->get($this->product->getSeoTitle()));
        $page->setDescription($replacer->get($this->product->getSeoDescription()));
        $page->setKeywords($replacer->get($this->product->getSeoKeywords()));
    }

    public function slotContentHead() {
        return $this->isNewProductPage ? null : $this->render('product/_contentHead', $this->params);
    }

    public function slotContent() {
        return $this->render($this->isNewProductPage ? 'product-page/content' : 'product/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute()
        . ($this->hasParam('categoryClass') ? ' ' . $this->getParam('categoryClass') : '')
        . $this->isNewProductPage ? ' product-card-new ' : '';
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        $categories = $this->product->getCategory();
        $category = array_pop($categories);

        $tag_params = [
            'prodid' => $this->product->getId(),
            'pagetype' => 'product',
            'pname' => $this->product->getName(),
            'pcat' => ($category) ? $category->getToken() : '',
            'pcat_upper' => $this->product->getMainCategory() ? $this->product->getMainCategory()->getToken() : '',
            'pvalue' => $this->product->getPrice()
        ];

        return parent::slotGoogleRemarketingJS($tag_params);
    }


    public function slotMetaOg() {

        if ($this->product->getDescription()) {
            $description = $this->product->getDescription();
        } elseif ($this->product->getTagline()) {
            $description = $this->product->getTagline();
        } else {
            $description = 'Enter - новый способ покупать. Любой из ' . number_format(\App::config()->product['totalCount'], 0, ',', ' ') . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
        }

        return "<meta property=\"og:title\" content=\"" . $this->escape($this->product->getName()) . "\"/>\r\n" .
            "<meta property=\"og:description\" content=\"" . $this->escape($description) . "\"/>\r\n" .
            "<meta property=\"og:image\" content=\"" . $this->escape($this->product->getImageUrl(3).'?'.time()) . "\"/>\r\n".
            "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
            "<meta property=\"og:type\" content=\"website\"/>\r\n".
            "<link rel=\"image_src\" href=\"". $this->escape($this->product->getImageUrl(3)). "\" />\r\n";
    }

    public function slotConfig() {
        $config = [
            'location'  => ['product'],
            'product'   => [
                'id' => $this->product->getId(),
                'isSlot' => (bool)$this->product->getSlotPartnerOffer(),
                'isOnlyFromPartner' => $this->product->isOnlyFromPartner(),
            ]
        ];

        return $this->tryRender('_config', ['config' => $config]);
    }

    public function slotUserbarContent() {
        return $this->render('product/_userbarContent', [
            'product'   => $this->product,
        ]);
    }

    public function slotUserbarContentData() {
        return [
            'target' => '.js-showTopBar',
            'productId' => $this->product->getId(),
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
        return !empty($html)
            ? $html . \View\Partners\Hubrus::addProductData($this->product)
            : '';
    }

    public function slotGoogleAnalytics()
    {
        return $this->tryRender('_googleAnalytics', ['product' => $this->getParam('product')]);
    }

    public function slotGetIntentJS() {
        if (!\App::config()->partners['GetIntent']['enabled']) {
            return '';
        }

        $data = [
            'productId' => $this->product ? (string)$this->product->getId() : '',
            'productPrice' => $this->product ? (string)$this->product->getPrice() : '',
            'categoryId' => $this->product && $this->product->getLastCategory() ? (string)$this->product->getLastCategory()->getId() : '',
        ];

        return '<div id="GetIntentJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }

    /**
     * Подготовка хлебных крошек
     */
    private function prepareBreadcrumbs() {
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];

            // Категории
            foreach ($this->product->getCategory() as $category) {
                $breadcrumbs[] = [
                    'name' => $category->getName(),
                    'url'  => $category->getLink(),
                ];
            }

            // Последний элемент
            $breadcrumbs[] = [
                'name' => $this->isNewProductPage ? 'Артикул ' . $this->product->getArticle() : $this->product->getName(),
                'url'  => $this->isNewProductPage ? null : $this->product->getLink(),
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }
}
