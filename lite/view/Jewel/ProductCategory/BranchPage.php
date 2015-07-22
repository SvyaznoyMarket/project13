<?php

namespace view\Jewel\ProductCategory;


class BranchPage extends \View\Jewel\ProductCategory\RootPage
{
    public function prepare(){
        parent::prepare();
        $this->prepareLinks();
    }

    /**
     * Подготовка
     */
    protected function prepareLinks() {
        /** @var $categories \Model\Product\Category\Entity[] */
        $categories = $this->getParam('category')->getChild();

        $links = [];
        foreach ($categories as $child) {
            $config = isset($categoryConfigById[$child->getId()]) ? $categoryConfigById[$child->getId()] : null;
            $productCount = $child->getProductCount();
            $totalText = '';

            if ( $productCount > 0 ) {
                $totalText = $productCount . ' ' . ($this->helper->numberChoice($productCount, array('товар', 'товара', 'товаров')));
            }

            $linkUrl = $child->getLink();
            $linkUrl .= \App::request()->getQueryString() ? (strpos($linkUrl, '?') === false ? '?' : '&') . \App::request()->getQueryString() : '';
            $linkUrl .= \App::request()->get('instore') ? (strpos($linkUrl, '?') === false ? '?' : '&') . 'instore=1' : '';

            $links[] = [
                'name'          => isset($config['name']) ? $config['name'] : $child->getName(),
                'url'           => $linkUrl,
                'image'         => (!empty($config['image']))
                    ? $config['image']
                    : $child->getImageUrl(3),
                'css'           => isset($config['css']) ? $config['css'] : null,
                'totalText'     => $totalText,
            ];
        }

        $this->setParam('links', $links);
    }
}