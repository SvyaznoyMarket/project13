<?php

class ProductCategoryTagView
{
  /** @var ProductCategoryEntity */
  public $category;
  /** @var ProductEntity[] */
  public $productList;
  /** @var int */
  public $productCount;

  public function getDataUrl()
  {
    return url_for('productCatalog__carousel', array('productCategory' => $this->category->getToken()));
  }
}
