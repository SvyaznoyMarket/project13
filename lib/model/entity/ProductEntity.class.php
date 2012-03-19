<?php

/**
 * Товар
 */
class ProductEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $token;

  /* @var ProductTypeEntity */
  private $type = null;

  /* @var BrandEntity */
  private $brand = null;

  /* @var ProductCategoryEntity[] */
  private $category = array();

  /* @var ProductAttributeEntity[] */
  private $attribute = array();

  /* @var string */
  private $name;

  /* @var string */
  private $prefix;

  /* @var string */
  private $article;

  /* @var string */
  private $barcode;

  /* @var boolean */
  private $isModel;

  /* @var string */
  private $announce;

  /* @var string */
  private $description;

  /* @var string */
  private $view;

  /* @var integer */
  private $score;

  /* @var string */
  private $link;

  /* @var string */
  private $tagline;

  /* @var string */
  private $defaultImage;

  /* @var integer */
  private $rating;

  /* @var integer */
  private $ratingQuantity;


  public function __construct()
  {
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  /**
   * @param \ProductTypeEntity $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return \ProductTypeEntity
   */
  public function getType()
  {
    return $this->type;
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

  /**
   * @return ProductCategoryEntity
   */
  public function getMainCategory()
  {
    return count($this->category) > 0 ? $this->category[0] : null;
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

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
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

  public function setAnnounce($announce)
  {
    $this->announce = $announce;
  }

  public function getAnnounce()
  {
    return $this->announce;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }

  /**
   * @return string
   */
  public function getView()
  {
    return $this->view;
  }

  /**
   * @param boolean $isModel
   */
  public function setIsModel($isModel)
  {
    $this->isModel = $isModel;
  }

  /**
   * @return boolean
   */
  public function isModel()
  {
    return $this->isModel;
  }

  /**
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * @param int $score
   */
  public function setScore($score)
  {
    $this->score = $score;
  }

  /**
   * @return int
   */
  public function getScore()
  {
    return $this->score;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * @param string $prefix
   */
  public function setPrefix($prefix)
  {
    $this->prefix = $prefix;
  }

  /**
   * @return string
   */
  public function getPrefix()
  {
    return $this->prefix;
  }

  /**
   * @param string $tagline
   */
  public function setTagline($tagline)
  {
    $this->tagline = $tagline;
  }

  /**
   * @return string
   */
  public function getTagline()
  {
    return $this->tagline;
  }

  /**
   * @param string $defaultImage
   */
  public function setDefaultImage($defaultImage)
  {
    $this->defaultImage = $defaultImage;
  }

  /**
   * @return string
   */
  public function getDefaultImage()
  {
    return $this->defaultImage;
  }

  /**
   * @param int $rating
   */
  public function setRating($rating)
  {
    $this->rating = $rating;
  }

  /**
   * @return int
   */
  public function getRating()
  {
    return $this->rating;
  }

  /**
   * @param int $ratingQuantity
   */
  public function setRatingQuantity($ratingQuantity)
  {
    $this->ratingQuantity = $ratingQuantity;
  }

  /**
   * @return int
   */
  public function getRatingQuantity()
  {
    return $this->ratingQuantity;
  }

}