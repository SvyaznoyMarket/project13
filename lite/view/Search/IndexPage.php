<?php

namespace view\Search;


use View\ProductCategory\CategoryPage;

class IndexPage extends CategoryPage
{
    protected $layout = 'layout/category.leaf';

    public function blockContent() {
        return $this->render('category/content.leaf', $this->params);
    }

    public function blockSearch() {
        return $this->render('category/_search', $this->params);
    }

    public function blockFilters() {

        $helper = \App::helper();
        $categories       = $this->getParam('categories');
        $productFilter  = $this->getParam('productFilter');

        return $this->render('category/_filters',[
            'baseUrl'       => $helper->url('search', ['q' => $this->getParam('searchQuery')]),
            'productFilter' => $productFilter,
            'categories'    => $categories,
            'openFilter'    => true,
            ])
//        . $helper->renderWithMustache('category/filters/selected.filters')
            ;
    }

}