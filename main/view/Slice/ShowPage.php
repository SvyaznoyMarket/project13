<?php

namespace View\Slice;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        parent::prepare();
        $this->setParam('breadcrumbs', call_user_func(function() {
            /** @var \Model\Product\Category\Entity|null $category */
            $category = $this->getParam('category');
            if (!$category || !$category->getId()) {
                return [];
            }

            $breadcrumbs = [];

            foreach ($category->getAncestor() as $ancestor) {
                $breadcrumbs[] = [
                    'url'  => $ancestor->getLink(),
                    'name' => $ancestor->getName(),
                ];
            }

            if ($category->getName()) {
                $breadcrumbs[] = [
                    'url'  => $category->getLink(),
                    'name' => $category->getName(),
                ];
            }

            $breadcrumbs = array_values($breadcrumbs);
            foreach ($breadcrumbs as $key => &$breadcrumb) {
                $breadcrumb['last'] = count($breadcrumbs) - 1 == $key ? true : false;
            }

            unset($breadcrumb);

            return $breadcrumbs;
        }));
    }

    public function slotContentHead() {
        return '';
    }

    public function slotContent() {
        return $this->render('slice/content', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotUserbarContent() {
        return $this->render('slice/_userbarContent', [
            'category'  => $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null,
            'slice'     => $this->getParam('slice')    instanceof \Model\Slice\Entity            ? $this->getParam('slice')    : null,
            'fixedBtn'  => [
                'link'       => '#',
                'name'       => 'Категории',
                'title'      => '',
                'class'      => '',
                'showCorner' => true,
            ],
        ]);
    }

    public function slotConfig() {
        $category = $this->getParam('category');
        return $this->tryRender('_config', ['config' => [
            'location' => ['listing'],
            'category' => $category instanceof \Model\Product\Category\Entity ? [
                'name' => $category->getName(),
                'ancestors' => array_map(function(\Model\Product\Category\Entity $category) {
                    return [
                        'name' => $category->getName(),
                    ];
                }, $category->getAncestor()),
            ] : [],
        ]]);
    }

    public function slotRelLink() {
        return
            parent::slotRelLink() . "\n" .
            $this->getPrevNextRelLinks();
    }

    /**
     * @return string
     */
    protected function getSort() {
        return \App::helper()->getCurrentSort();
    }

    public function slotMicroformats() {
        return
            parent::slotMicroformats() .
            call_user_func(function() {
                $minPrice = null;
                $maxPrice = null;
                $productCount = null;

                /** @var \Model\Product\Filter|null $productFilter */
                $productFilter = $this->getParam('productFilter') instanceof \Model\Product\Filter ? $this->getParam('productFilter') : null;
                /** @var \Iterator\EntityPager $productPager */
                $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
                /** @var \Model\Product\Filter\Entity|null $priceProperty */
                $priceProperty = $productFilter ? $productFilter->getPriceProperty() : null;

                if (
                    $productFilter &&
                    count($productFilter->getValues()) == 0 && // На данный момент примение фильтров не изменяет мин. и макс. значения цен, поэтому в этом случае данные значения будут некорректными
                    $productPager
                ) {
                    $productCount = $productPager->count();

                    if ($productCount == 1) {
                        $productPager->rewind();
                        /** @var \Model\Product\Entity|null $product */
                        $product = $productPager->current();
                        if ($product) {
                            $minPrice = $product->getPrice();
                            $maxPrice = $product->getPrice();
                        }
                    } else if ($productCount && $priceProperty) {
                        $minPrice = $priceProperty->getMin();
                        $maxPrice = $priceProperty->getMax();
                    }
                }

                if ($minPrice !== null && $maxPrice && $productCount) {
                    return '<script type="application/ld+json">' . json_encode([
                        '@context' => 'http://schema.org/',
                        '@type' => 'Product',
                        'name' => $this->getParam('title'),
                        'offers' => [
                            '@type' => 'AggregateOffer',
                            'priceCurrency' => 'RUB',
                            'lowprice' => $minPrice,
                            'highprice' => $maxPrice,
                            'offerCount' => $productCount,
                        ],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
                } else {
                    return '';
                }
            });
    }

    public function slotGdeSlonJS() {
        /** @var \Iterator\EntityPager|null $productPager */
        $productPager = $this->getParam('productPager');
        /** @var \Model\Slice\Entity|null $slice */
        $slice = $this->getParam('slice');
        /** @var \Model\Product\Category\Entity|null $category */
        $category = $this->getParam('category');

        $codes = '';
        foreach ($productPager as $product) {
            /** @var \Model\Product\Entity $product */
            $codes .= $product->id.':'.$product->getPrice().',';
        }

        return '<script async="true" type="text/javascript" src="https://www.gdeslon.ru/landing.js?mode=list&codes='.urlencode(mb_substr($codes, 0, -1, 'utf-8')).'&mid=81901"></script>';
    }
}
