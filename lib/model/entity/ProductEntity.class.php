<?php

/**
 * Товар
 * @todo check label
 * @todo check is_in_sale
 */
class ProductEntity
{
  /* @var integer */
  private $id;
  /** @var int */
  private $viewId;
  /** @var int */
  private $setId;

  /* @var string */
  private $token;

  /* @var ProductTypeEntity */
  private $type = null;

  /* @var BrandEntity */
  private $brand = null;

  /* @var ProductCategoryEntity[] */
  private $categoryList = array();

  /* @var ProductAttributeEntity[] */
  private $attribute = array();

  /* @var string */
  private $name;
  /** @var string */
  private $nameWeb;

  /* @var string */
  private $prefix;

  /* @var string */
  private $article;

  /* @var string */
  private $barcode;

  /* @var boolean */
  private $isModel;
  /** @var int  */
  private $modelId;

  /** @var boolean */
  private $isPrimaryLine;

  /* @var string */
  private $announce;

  /* @var string */
  private $description;
  /** @var string */
  private $mediaImage;

  /* @var string */
  private $view;

  /* @var integer */
  private $score;

  /* @var string */
  private $link;

  /* @var string */
  private $tagline;

  /* @var integer */
  private $rating;

  /* @var integer */
  private $ratingQuantity;
  /** @var int */
  private $commentsNum;
  /** @var int */
  private $price;


  public function __construct($data = array())
  {
    if(array_key_exists('id', $data))               $this->setId($data['id']);
    if(array_key_exists('view_id', $data))          $this->setViewId($data['view_id']);
    if(array_key_exists('set_id', $data))           $this->setSetId($data['set_id']);
    if(array_key_exists('is_model', $data))         $this->setIsModel($data['is_model']);
    if(array_key_exists('is_primary_line', $data))  $this->setIsPrimaryLine($data['is_primary_line']);
    if(array_key_exists('model_id', $data))         $this->setModelId($data['model_id']);
    if(array_key_exists('score', $data))            $this->setScore($data['score']);
    if(array_key_exists('name', $data))             $this->setName($data['name']);
    if(array_key_exists('link', $data))             $this->setLink($data['link']);
    if(array_key_exists('token', $data))            $this->setToken($data['token']);
    if(array_key_exists('name_web', $data))         $this->setNameWeb($data['name_web']);
    if(array_key_exists('prefix', $data))           $this->setPrefix($data['prefix']);
    if(array_key_exists('article', $data))          $this->setArticle($data['article']);
    if(array_key_exists('bar_code', $data))         $this->setBarcode($data['bar_code']);
    if(array_key_exists('tagline', $data))          $this->setTagline($data['tagline']);
    if(array_key_exists('announce', $data))         $this->setAnnounce($data['announce']);
    if(array_key_exists('description', $data))      $this->setDescription($data['description']);
    if(array_key_exists('media_image', $data))      $this->setMediaImage($data['media_image']);
    if(array_key_exists('rating', $data))           $this->setRating($data['rating']);
    if(array_key_exists('rating_count', $data))     $this->setRatingCount($data['rating_count']);
    if(array_key_exists('comments_num', $data))     $this->setCommentsNum($data['comments_num']);
    if(array_key_exists('price', $data))            $this->setPrice($data['price']);
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
  public function setType(ProductTypeEntity $type)
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

  public function setCategoryList(array $category)
  {
    $this->categoryList = $category;
  }

  public function addCategory(ProductCategoryEntity $category)
  {
    $this->categoryList[] = $category;
  }

  /**
   * @return ProductCategoryEntity
   */
  public function getCategoryList()
  {
    return $this->categoryList;
  }

  /**
   * @return ProductCategoryEntity
   */
  public function getMainCategory()
  {
    return count($this->categoryList) > 0 ? $this->categoryList[0] : null;
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
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId = 1)
  {
    if($this->mediaImage){
      $urls = sfConfig::get('app_product_photo_url');
      return $urls[$viewId].$this->mediaImage;
    }
    else{
      return null;
    }
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
  public function setRatingCount($ratingQuantity)
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

  /**
   * @param int $viewId
   */
  public function setViewId($viewId)
  {
    $this->viewId = $viewId;
  }

  /**
   * @return int
   */
  public function getViewId()
  {
    return $this->viewId;
  }

  /**
   * @param int $setId
   */
  public function setSetId($setId)
  {
    $this->setId = $setId;
  }

  /**
   * @return int
   */
  public function getSetId()
  {
    return $this->setId;
  }

  /**
   * @param boolean $isPrimaryLine
   */
  public function setIsPrimaryLine($isPrimaryLine)
  {
    $this->isPrimaryLine = $isPrimaryLine;
  }

  /**
   * @return boolean
   */
  public function getIsPrimaryLine()
  {
    return $this->isPrimaryLine;
  }

  /**
   * @param int $modelId
   */
  public function setModelId($modelId)
  {
    $this->modelId = $modelId;
  }

  /**
   * @return int
   */
  public function getModelId()
  {
    return $this->modelId;
  }

  /**
   * @param string $nameWeb
   */
  public function setNameWeb($nameWeb)
  {
    $this->nameWeb = $nameWeb;
  }

  /**
   * @return string
   */
  public function getNameWeb()
  {
    return $this->nameWeb;
  }

  /**
   * @param string $mediaImage
   */
  public function setMediaImage($mediaImage)
  {
    $this->mediaImage = $mediaImage;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
  }

  /**
   * @param int $commentsNum
   */
  public function setCommentsNum($commentsNum)
  {
    $this->commentsNum = $commentsNum;
  }

  /**
   * @return int
   */
  public function getCommentsNum()
  {
    return $this->commentsNum;
  }

  /**
   * @param int $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return int
   */
  public function getPrice()
  {
    return $this->price;
  }

}