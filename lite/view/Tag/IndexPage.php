<?php

namespace View\Tag;

class IndexPage extends \View\ProductCategory\CategoryPage {
    protected $layout = 'layout/category.leaf';

    public function blockContent() {
        return $this->render('category/content.leaf', $this->params);
    }

    /** Фильтры в листингах
     * @return string
     */
    public function blockFilters() {
        $tag            = $this->getParam('tag');
        $categories     = $this->getParam('categories');
        $productFilter  = $this->getParam('productFilter');

        return $this->render('category/_filters',[
            'categories'    => $categories,
            'baseUrl'       => $tag ? \App::helper()->url('tag', ['tagToken' => $tag->getToken()]) : '',
            'productFilter' => $productFilter,
            'openFilter'    => true,
        ]);
    }
}