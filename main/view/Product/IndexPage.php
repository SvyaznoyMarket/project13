<?php

namespace View\Product;

use \Model\Product\Entity as Product;
use Model\ClosedSale\ClosedSaleEntity;
use Model\Product\Label;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';
    /** @var Product */
    protected $product;

    public function prepare() {
        $product = $this->product = $this->getParam('product', new Product());

        $this->flPrecheckoutData['fl-action']   = 'track-item-view';
        $this->flPrecheckoutData['fl-item-id']  = $product->id;

        // Хлебные крошки
        $this->prepareBreadcrumbs();
        // Видео и 3D
        $this->prepareMedia();

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

        if (!$this->hasParam('sender2')) $this->setParam('sender2', $product->isOnlyFromPartner() && !$product->getSlotPartnerOffer() ? 'marketplace' : '');
        if (!$this->hasParam('isKit')) $this->setParam('isKit', (bool)$product->getKit());

        $this->closedSale();
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
        return null;
    }

    public function slotContent() {
        return $this->render('product-page/content', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute()
        . ($this->hasParam('categoryClass') ? ' ' . $this->getParam('categoryClass') : '')
        . ((!$this->getParam('product') || !$this->getParam('product')->getSlotPartnerOffer()) ? ' product-card-new ' : '');
    }

    public function slotGoogleRemarketingJS($tagParams = []) {
        $categories = $this->product->getCategory();
        $category = array_pop($categories);

        $tag_params = [
            'prodid' => $this->product->getId(),
            'pagetype' => 'product',
            'pname' => $this->product->getName(),
            'pcat' => ($category) ? $category->getToken() : '',
            'pcat_upper' => $this->product->getRootCategory() ? $this->product->getRootCategory()->getToken() : '',
            'pvalue' => $this->product->getPrice(),
        ];

        return parent::slotGoogleRemarketingJS($tag_params);
    }


    public function slotMetaOg() {

        if ($this->product->getDescription()) {
            $description = $this->product->getDescription();
        } elseif ($this->product->getTagline()) {
            $description = $this->product->getTagline();
        } else {
            $description = \App::config()->description;
        }

        return "<meta property=\"og:title\" content=\"" . $this->escape($this->product->getName()) . "\"/>\r\n" .
            "<meta property=\"og:description\" content=\"" . $this->escape($description) . "\"/>\r\n" .
            "<meta property=\"og:image\" content=\"" . $this->escape($this->product->getMainImageUrl('product_120').'?'.time()) . "\"/>\r\n".
            "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
            "<meta property=\"og:type\" content=\"website\"/>\r\n".
            "<link rel=\"image_src\" href=\"". $this->escape($this->product->getMainImageUrl('product_120')). "\" />\r\n";
    }

    public function slotConfig() {
        $reviewsData = $this->getParam('reviewsData');
        $config = [
            'location'  => ['product'],
            'product'   => [
                'id' => $this->product->id,
                'ui' => $this->product->ui,
                'isSlot' => (bool)$this->product->getSlotPartnerOffer(),
                'isOnlyFromPartner' => $this->product->isOnlyFromPartner(),
                'avgScore' => empty($reviewsData['avg_score']) ? 0 : $reviewsData['avg_score'],
                'firstPageAvgScore' => empty($reviewsData['current_page_avg_score']) ? 0 : $reviewsData['current_page_avg_score'],
                'category' => [
                    'name' => $this->product->getParentCategory() ? $this->product->getParentCategory()->getName() : ''
                ],
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
            'picture'   => $this->product->getMainImageUrl('product_120'),
            'name'      => $this->product->getName(),
            'category'  => $this->product->getParentCategory() ? $this->product->getParentCategory()->getId() : null
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
            'categoryId' => $this->product && $this->product->getParentCategory() ? (string)$this->product->getParentCategory()->getId() : '',
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
                'name' => 'Артикул ' . $this->product->getArticle(),
                'url'  => null,
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    /**
     * Подготовка видео и 3D
     */
    private function prepareMedia(){

        $helper = \App::helper();
        $videoHtml = null;
        $properties3D = ['type' => null, 'url' => null];

        foreach ($this->product->medias as $media) {
            $source = $media->getSource('reference');
            switch ($media->provider) {
                case 'vimeo':
                    if ($source) {
                        $width = 700;
                        $height = ceil($width / ($source->width / $source->height));
                        $videoHtml = sprintf(
                            '<iframe data-src="%s?autoplay=1" width="%s" height="%s" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
                            $helper->escape($source->url), $width, $height);
                    }
                    break;
                case 'youtube':
                    if ($source) {
                        $width = 700;
                        $height = ceil($width / ($source->width / $source->height));
                        $videoHtml = sprintf(
                            '<iframe data-src="//www.youtube.com/embed/%s?autoplay=1" width="%s" height="%s" frameborder="0" allowfullscreen></iframe>',
                            $helper->escape($source->id), $width, $height);
                    }
                    break;
                case 'megavisor':
                    if ($source) {
                        $properties3D['type'] = 'swf';
                        $properties3D['url'] = 'http://media.megavisor.com/player/player.swf?uuid=' . urlencode($source->id);
                    }
                    break;
                case 'swf':
                    if ($source){
                        $properties3D['type'] = 'swf';
                        $properties3D['url'] = $source->url;
                    }

                    break;
                case 'maybe3d':
                    /*if ($source = $media->getSource('html5')) {
                        $properties3D['type'] = 'html5';
                        $properties3D['url'] = $source->url;
                        $properties3D['id'] = $source->id;
                    } else*/ if ($source = $media->getSource('swf')) {
                        $properties3D['type'] = 'swf';
                        $properties3D['url'] = $source->url;
                    }
                    break;
            }
        }

        $this->setParam('videoHtml', $videoHtml);
        $this->setParam('properties3D', $properties3D);
    }

    public function slotMyThings($data) {
        return parent::slotMyThings([
            'Action'    => '1010',
            'ProductId' => (string)$this->product->getId()
        ]);
    }

    /**
     * Изменяем хлебные крошки для товара из закрытой распродажи и добавляем Label к товару для счётчика справа
     */
    public function closedSale()
    {
        /** @var ClosedSaleEntity $sale */
        if (!$sale = $this->getParam('closedSale')) {
            return;
        }

        $this->addMeta('robots', 'none');

        // Модифицируем хлебные крошки
        $breadcrumbs = [
            [
                'name' => 'Секретная распродажа',
                'url'  => $this->url('sale.all')
            ],
            [
                'name' => $sale->name,
                'url'  => $this->url('sale.one', ['uid' => $sale->uid])
            ],
            [
                'name' => $this->product->getRootCategory()->getName(),
                'url'  => $this->url('sale.one', ['uid' => $sale->uid, 'categoryId' => $this->product->getRootCategory()->getId()])
            ],
            [
                'name' => 'Артикул ' . $this->product->getArticle()
            ]
        ];

        $this->setParam('breadcrumbs', $breadcrumbs);

        // Акция
        $label = new Label([]);
        $label->expires = $sale->endsAt;
        $label->url = $this->url('sale.one', ['uid' => $sale->uid]);
        $this->product->setLabel($label);

        $this->setParam('product', $this->product);

    }

    public function slotSolowayJS() {
        if (!\App::config()->partners['soloway']['enabled']) {
            return '';
        }

        return '<div id="solowayJS" class="jsanalytics" data-vars="' . $this->json([
            'type' => 'product',
            'product' => [
                'ui' => $this->product->ui,
                'category' => [
                    'ui' => $this->product->getParentCategory() ? $this->product->getParentCategory()->ui : '',
                ],
            ],
        ]) . '"></div>';
    }
}
