<?php

namespace View\Jewel\Product;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }

        // if (is_array($this->getParam('productVideos'))) {
        //     $productVideos = $this->getParam('productVideos');
        //     $productVideos = reset($productVideos);
        //     if ($productVideos instanceof \Model\Product\Video\Entity) {
        //         $this->addJavascript('/js/swfobject.js');
        //     }
        // }

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

        // seo
        $page = new \Model\Page\Entity();

        $page->setTitle(sprintf(
            '%s - купить по цене %s руб. в Москве, %s - характеристиками и описанием и фото от интернет-магазина Enter.ru',
            $product->getName(),
            $product->getPrice(),
            $product->getName()
        ));
        $page->setDescription(sprintf(
            'Интернет магазин Enter.ru предлагает купить: %s по цене %s руб. На нашем сайте Вы найдете подробное описание и характеристики товара %s с фото. Заказать понравившийся товар с доставкой по Москве можно у нас на сайте или по телефону ' . \App::config()->company['phone'] . '.',
            $product->getName(),
            $product->getPrice(),
            $product->getName()
        ));
        $page->setKeywords(sprintf('%s Москва интернет магазин купить куплю заказать продажа цены', $product->getName()));

        try {
            $this->applySeoPattern($page);
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $this->setTitle($page->getTitle());
        $this->addMeta('description', $page->getDescription());
        $this->addMeta('keywords', $page->getKeywords());
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('jewel/product/_contentHead', $this->params);
    }

    public function slotContent() {
        return $this->render('jewel/product/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_card';
    }

    public function slotUserbar() {
        return $this->render('jewel/_userbar');
    }

    public function slotInnerJavascript() {
        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        $categories = $product->getCategory();
        $category = array_pop($categories);

        return ''
            . ($product ? $this->tryrender('jewel/product/partner-counter/_etargeting', array('product' => $product)) : '')
            . "\n\n"
            . ($product ? $this->render('_remarketingGoogle', ['tag_params' => ['prodid' => $product->getId(), 'pagetype' => 'product', 'pname' => $product->getName(), 'pcat' => ($category) ? $category->getToken() : '', 'pvalue' => $product->getPrice()]]) : '')
            . "\n\n"
            . $this->render('_innerJavascript');
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
                "<meta property=\"og:image\" content=\"" . $this->escape($product->getImageUrl(3)) . "\"/>\r\n".
                "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
                "<meta property=\"og:type\" content=\"website\"/>\r\n";
    }

    public function slotAdvanceSeoCounter() {
        /** @var \Model\Product\Entity $product  */
        $product = $this->getParam('product');

        if (!$product) {
            return '';
        }

        return \App::config()->analytics['enabled']
            ? ("<div id=\"marketgidProd\" class=\"jsanalytics\"></div>\r\n")
            : '';
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
                'categoryId' => $product->getMainCategory() ? $product->getMainCategory()->getId() : 0,
            );
        }

        return \App::config()->analytics['enabled'] ? "<div id=\"adriverProduct\" data-vars='".json_encode( $data )."' class=\"jsanalytics\"></div>\r\n" : '';
    }

    private function applySeoPattern(\Model\Page\Entity $page) {
        $dataStore = \App::dataStoreClient();

        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }
        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $product->getCategory();
        if (!(bool)$categories) {
            return;
        }

        $region = \App::user()->getRegion();

        $seoTemplate = null;
        $categoryTokens = [];
        foreach ($categories as $iCategory) {
            $categoryTokens[] = $iCategory->getToken();
        }
        /** @var $category \Model\Product\Category\Entity */
        $category = end($categories);

        $dataStore->addQuery(sprintf('seo/product/%s/%s.json', implode('/', $categoryTokens), $product->getToken()), [], function ($data) use (&$seoTemplate) {
            $seoTemplate = array_merge([
                'title'       => null,
                'description' => null,
                'keywords'    => null,
            ], $data);
        });

        // данные для шаблона
        $patterns = [
            'категория' => [$category->getName()],
            'город'     => [$region->getName()],
            'сайт'      => null,
            'товар'     => $product->getName(),
            'анонс товара'     => $product->getAnnounce(),
            'цена'      => $product->getPrice() . ' руб',
        ];
        $dataStore->addQuery(sprintf('inflect/product-category/%s.json', $category->getId()), [], function($data) use (&$patterns) {
            if ($data) $patterns['категория'] = $data;
        });
        $dataStore->addQuery(sprintf('inflect/region/%s.json', $region->getId()), [], function($data) use (&$patterns) {
            if ($data) $patterns['город'] = $data;
        });
        $dataStore->addQuery('inflect/сайт.json', [], function($data) use (&$patterns) {
            if ($data) $patterns['сайт'] = $data;
        });

        $dataStore->execute();

        // переменные для характеристик товара
        $properties = $product->getProperty();
        foreach ($properties as $property) {
            if($property->getValue() == 'true') {
                $value = 'да';
            } elseif($property->getValue() == 'false') {
                $value = 'нет';
            } elseif($property->getValue()) {
                $value = $property->getValue();
            } else {
                $value = 'не указано';
            }
            $patterns[mb_strtolower($property->getName())] = $value;
        }

        if (!$seoTemplate) return;

        $replacer = new \Util\InflectReplacer($patterns);
        if ($value = $replacer->get($seoTemplate['title'])) {
            $page->setTitle($value);
        }
        if ($value = $replacer->get($seoTemplate['description'])) {
            $page->setDescription($value);
        }
        if ($value = $replacer->get($seoTemplate['keywords'])) {
            $page->setKeywords($value);
        }
    }
}
