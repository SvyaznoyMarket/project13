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

        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = $this->getBreadcrumbsPath();
            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        if ($productPager && $productPager->getPage() > 1) {
            $pageSeoText = 'Страница ' . $productPager->getPage() . ' - ' . implode(', ', call_user_func(function() use($category, $brand, $helper) {
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
                $this->setTitle('Цены на ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' | Купить ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' в Enter - стоимость, отзывы, каталог товаров');
                $this->addMeta('description', 'В нашем интернет магазине Enter.ru ты можешь купить недорого ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' с доставкой. Выгодные цены и быстрая доставка. Звоните ☎ ' . \App::config()->company['phone']);
            } else {
                $this->setTitle('Купить ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' в интернет-магазине Enter | Цена на ' . $helper->lcfirst($category->inflectedNames->accusativus ?: $category->name) . ' онлайн - полный каталог, отзывы и доставка по России');
                $this->addMeta('description', $helper->lcfirst($category->inflectedNames->nominativus ?: $category->name) . ' - полный каталог электроники с ценами, фото и отзывами. Купить товары в интернет-магазине Enter легко! Звоните ☎ ' . \App::config()->company['phone']);
            }
        }
    }

    public function slotContentHead() {
        $ret = '';

        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        // if (!$this->hasParam('breadcrumbs')) {
        //     $this->setParam('breadcrumbs', []);
        // }
        $this->setParam('breadcrumbs', []);

        $contentHead = $this->render('_contentHead', array_merge($this->params, ['title' => null])); // TODO: осторожно, костыль

        if ($contentHead) $ret .= $contentHead;

        return $ret;

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

}