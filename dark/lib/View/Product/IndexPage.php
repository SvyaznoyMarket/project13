<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {

  /** @var string */
  protected $layout  = 'layout-default-oneColumn';

  public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }

  public function slotBodyDataAttribute() {
    return 'product_card';
  }
}
