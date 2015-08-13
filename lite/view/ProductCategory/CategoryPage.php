<?php

namespace view\ProductCategory;


use View\LiteLayout;

class CategoryPage extends LiteLayout
{

    /** @var  \Model\Product\Category\Entity */
    protected $category;

    public function prepare() {
        parent::prepare();
        $this->category = $this->getParam('category');
    }

    public function blockFixedUserbar()
    {
        return $this->render('category/_userbar.fixed', ['category' => $this->category]);
    }

    /** Фильтры в листингах
     * @return string
     */
    public function blockFilters() {

        $helper = \App::helper();
        $category       = $this->getParam('category');
        $productFilter  = $this->getParam('productFilter');
        $promoStyle     = $this->getParam('promoStyle');

        return $this->render('category/_filters',[
            'baseUrl'       => $helper->url('product.category', ['categoryPath' => $category->getPath()]),
            'productFilter' => $productFilter,
            'openFilter'    => false,
            'promoStyle'    => $promoStyle,
            ])
//            . $helper->renderWithMustache('category/filters/selected.filters')
            ;
    }

}