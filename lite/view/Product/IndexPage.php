<?php

namespace view\Product;


use View\LiteLayout;

class IndexPage extends LiteLayout
{
    protected $layout = 'layout/product';
    /** @var \Model\Product\Entity */
    protected $product;

    public function prepare() {
        parent::prepare();
        $this->product = $this->getParam('product');
        $this->prepareBreadcrumbs();
        $this->prepareMedia();
        if (!$this->hasParam('isKit')) $this->setParam('isKit', (bool)$this->product->getKit());
    }


    public function blockContent() {
        return $this->render('product/content', $this->params);
    }

    public function blockFixedUserbar() {
        return $this->render('product/_userbar.fixed', ['product' => $this->product]);
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
                'url'  => null
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    /**
     * Подготовка видео и 3D
     */
    private function prepareMedia(){
        $videoAnd3d = (new \Model\Product\Repository())->getVideoAnd3d($this->product);
        $this->setParam('videoHtml', $videoAnd3d['videoHtml']);
        $this->setParam('properties3D', $videoAnd3d['properties3D']);
    }
}