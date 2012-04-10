<?php

/**
 * Товар
 */
class ProductEntity
{
  /* @var integer */
  private $id;

  /* @var BrandEntity */
  private $brand;

  /* @var ProductCategoryEntity[] */
  private $category = array();

  /* @var ProductAttributeEntity[] */
  private $attribute = array();

  /* @var string */
  private $announce;

  /* @var string */
  private $article;

  /* @var string */
  private $barcode;

  /* @var string */
  private $description;


  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }


  public function __construct()
  {
    $this->brand = new BrandEntity();
  }

  public function setBrand(BrandEntity $brand)
  {
    return $this->brand = $brand;
  }

  /**
   * @return BrandEntity
   */
  public function getBrand()
  {
    return $this->brand;
  }

  public function setCategory(array $category)
  {
    $this->category = $category;
  }

  public function addCategory(ProductCategoryEntity $category)
  {
    $this->category[] = $category;
  }

  /**
   * @return ProductCategoryEntity
   */
  public function getCategory()
  {
    return $this->category;
  }

  public function setAttribute(array $attribute)
  {
    $this->attribute = $attribute;
  }

  public function addAttribute(ProductAttributeEntity $property)
  {
    $this->attribute[] = $property;
  }

  /**
   * @return ProductAttributeEntity[]
   */
  public function getAttribute()
  {
    return $this->attribute;
  }


  public function setAnnounce($announce)
  {
    $this->announce = $announce;
  }

  public function getAnnounce()
  {
    return $this->announce;
  }

  public function setArticle($article)
  {
    $this->article = $article;
  }

  public function getArticle()
  {
    return $this->article;
  }

  public function setBarcode($barcode)
  {
    $this->barcode = $barcode;
  }

  public function getBarcode()
  {
    return $this->barcode;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function getDescription()
  {
    return $this->description;
  }
}