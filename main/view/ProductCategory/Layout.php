<?php

namespace View\ProductCategory;

use \Model\Product\Category\Entity as Category;

abstract class Layout extends \View\DefaultLayout {

    use LayoutTrait;

    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $helper = \App::helper();

        /** @var Category $category */
        $category = $this->getParam('category', new Category());

        /** @var $brand \Model\Brand\Entity */
        $brand = $this->getParam('brand');

        /** @var $productPager \Iterator\EntityPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;

        if ($category->isTchibo()) {
            $this->useMenuHamburger = true;
        }

        $this->flPrecheckoutData['fl-action'] = 'track-category-view';
        $this->flPrecheckoutData['fl-category-id'] = $category->id;

        if (!$this->getParam('title')) {
            $this->setParam('title', $category->getName());
        }

        $this->setParam('breadcrumbs', call_user_func(function() {
            /** @var \Model\Product\Category\Entity|null $category */
            $category = $this->getParam('category');
            if (!$category) {
                return [];
            }

            /** @var \Model\Product\Category\Entity[] $categories */
            $categories = $category->getAncestor();

            $breadcrumbs = [];
            $count = count($categories);
            $i = 0;
            foreach ($categories as $ancestor) {
                $i++;

                $breadcrumbs[] = [
                    'url'  => $ancestor->getLink(),
                    'name' => $ancestor->getName(),
                    'last' => $i == $count,
                ];
            }

            return $breadcrumbs;
        }));

        if ($productPager && $productPager->getPage() > 1) {
            $pageSeoText = 'Страница ' . $productPager->getPage() . ' - ' . implode(' > ', call_user_func(function() use($category, $brand, $helper) {
                $parts = [];

                foreach ($category->getAncestor() as $ancestorCategory) {
                    $parts[] = $ancestorCategory->name;
                }

                if ($category->name) {
                    if ($brand && $brand->name) {
                        $parts[] = trim(preg_replace('/' . $brand->name . '$/', '', $category->name));
                    } else {
                        $parts[] = $category->name;
                    }
                }

                if ($brand) {
                    $parts[] = $brand->name;
                }

                return $parts;
            }));
            
            $this->setTitle($pageSeoText);
            $this->addMeta('description', 'В нашем интернет магазине Enter.ru ты можешь купить с доставкой. ' . $pageSeoText);
        } else {
            if ($category->getSeoTitle() && $category->getSeoDescription() && !$brand) {
                $this->setTitle($category->getSeoTitle());
                $this->addMeta('description', $category->getSeoDescription());
            } else if ($category->getLevel() == 1) {
                $this->setTitle('Купить ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' с доставкой | Продажа ' . $helper->lcfirst($category->inflectedNames->genitivus ?: $category->name) . ' в Москве и России - интернет-магазин Enter.ru');
                $this->addMeta('description', 'Enter — это интернет-магазин ' . $helper->lcfirst($category->inflectedNames->genitivus ?: $category->name) . ', в котором вы найдете абсолютно любой товар. Продажа ' . $helper->lcfirst($category->inflectedNames->genitivus ?: $category->name) . ' по всей России. Звоните ☎ ' . \App::config()->company['phone']);
            } else if ($brand) {
                // Если при вызове метода для получения категории ему был передан бренд, то seo данные бренда заменят seo данные категории
                if ($category->getSeoTitle() && $category->getSeoDescription()) {
                    $this->setTitle($category->getSeoTitle());
                    $this->addMeta('description', $category->getSeoDescription());
                } else {
                    $this->setTitle('Цены на ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' | Купить ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' в Enter - стоимость, отзывы, каталог товаров');
                    $this->addMeta('description', 'В нашем интернет магазине Enter.ru ты можешь купить недорого ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' с доставкой. Выгодные цены и быстрая доставка. Звоните ☎ ' . \App::config()->company['phone']);
                }
            } else {
                $this->setTitle('Купить ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' в интернет-магазине Enter | Цена на ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' онлайн - полный каталог, отзывы и доставка по России');
                $this->addMeta('description', $helper->lcfirst($category->inflectedNames->nominativus ?: $category->name) . ' - полный каталог электроники с ценами, фото и отзывами. Купить товары в интернет-магазине Enter легко! Звоните ☎ ' . \App::config()->company['phone']);
            }
        }
    }

    public function slotContentHead() {
        return '';
    }

    public function slotMetaOg() {
        /** @var \Model\Product\Category\Entity $category  */
        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) return '';

        return "<meta property=\"og:title\" content=\"" . $this->escape($category->getName()) . "\"/>\r\n" .
            "<meta property=\"og:image\" content=\"" . $this->escape($category->getImageUrl().'?'.time()) . "\"/>\r\n".
            "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
            "<meta property=\"og:type\" content=\"website\"/>\r\n";

    }

    public function slotMicroformats() {
        return
            parent::slotMicroformats() .
            call_user_func(function() {
                $minPrice = null;
                $maxPrice = null;
                $productCount = null;

                if ($this instanceof \View\ProductCategory\Grid\ManualGridPage) {
                    /** @var $availableProducts \Model\Product\Entity[] */
                    $availableProducts = array_filter($this->getParam('productsByUi'), function($product) {
                        return $product instanceof \Model\Product\Entity && $product->isAvailable();
                    });

                    foreach ($availableProducts as $product) {
                        if ($product->getPrice() < $minPrice || $minPrice === null) {
                            $minPrice = $product->getPrice();
                        }

                        if ($product->getPrice() > $maxPrice || $maxPrice === null) {
                            $maxPrice = $product->getPrice();
                        }
                    }

                    $productCount = count($availableProducts);
                } else {
                    /** @var \Model\Product\Filter|null $productFilter */
                    $productFilter = $this->getParam('productFilter') instanceof \Model\Product\Filter ? $this->getParam('productFilter') : null;
                    /** @var \Iterator\EntityPager $productPager */
                    $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
                    /** @var \Model\Product\Category\Entity|null $category */
                    $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
                    /** @var \Model\Product\Filter\Entity|null $priceProperty */
                    $priceProperty = $productFilter ? $productFilter->getPriceProperty() : null;

                    if (
                        $productFilter &&
                        count($productFilter->getValues()) == 0 && // На данный момент примение фильтров не изменяет мин. и макс. значения цен, поэтому в этом случае данные значения будут некорректными
                        $productPager &&
                        (
                            $this instanceof \View\Jewel\ProductCategory\LeafPage ||
                            ($this instanceof \View\ProductCategory\LeafPage && $category && !$category->isGrid())
                        )
                    ) {
                        $productCount = $this->getParam('productCount'); // Кол-во без учёта баннера

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
}