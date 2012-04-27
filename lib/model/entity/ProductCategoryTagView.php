<?php

class ProductCategoryTagView
{
  /** @var ProductCategoryEntity */
  public $category;
  /** @var ProductEntity[] */
  public $productList;
  /** @var int */
  public $productCount;

  private $request;

  public function getDataUrl()
  {
    return url_for('productCatalog_carousel', array('productCategory' => $this->category->getToken()));
  }

  public function setRequest(array $request)
  {
    $this->request = $request;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    if($this->category){
      if($this->request)
        return $this->category->getLink() . '?' . http_build_query($this->request);
      else
        return $this->category->getLink();
    }
    else
      return null;
  }
}
