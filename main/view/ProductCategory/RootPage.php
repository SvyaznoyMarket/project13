<?php

namespace View\ProductCategory;

class RootPage extends Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('product-category/page-root', $this->params);
    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        $return = null;
        if ($this->hasParam('categoryPath')) {
            switch ($this->getParam('categoryPath')) {
                case 'furniture':
                    $id = 22253;
                    break;
                case 'household':
                    $id = 22254;
                    break;
                case 'appliances':
                    $id = 22255;
                    break;
                case 'electronics':
                    $id = 22256;
                    break;
                case 'children':
                    $id = 22257;
                    break;
                case 'jewel':
                    $id = 22258;
                    break;
                case 'parfyumeriya-i-kosmetika':
                    $id = 22259;
                    break;
                case 'gift_hobby':
                    $id = 22260;
                    break;
                case 'do_it_yourself':
                    $id = 22261;
                    break;
                case 'tovari-dlya-givotnih':
                    $id = 22262;
                    break;
                case 'sport':
                    $id = 25011;
                    break;
                case 'aksessuari-dlya-avtomobiley-225':
                    $id = 25012;
                    break;
                default:
                    $id = null;
            }

            $return = '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => $id]) . '"></div>';
        }

        return $return;
    }

    public function slotMyThings($data) {
        /** @var $category \Model\Product\Category\Entity */
        return parent::slotMyThings([
            'Action'    => '1011',
            'Category'  => ($category = $this->getParam('category')) && $category instanceof \Model\Product\Category\Entity ? $category->getName() : null
        ]);
    }


}
