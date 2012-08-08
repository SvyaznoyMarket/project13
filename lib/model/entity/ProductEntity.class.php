<?php

class ProductEntity
{

  /**
   * Дефолтное отображение связанных товаров - аксессуары сверху, смежные товары в футере
   */
  const DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE = 1;

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
  private $attributeList = array();
  /* @var ProductAttributeEntity[] */
  private $attributeMap = array();
  /** @var ProductPropertyGroupEntity[] */
  private $propertyGroupList = array();

  /** @var ProductLabelEntity[] */
  private $labelList = array();

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

  /** @var ProductModelEntity */
  private $model;

  /** @var boolean */
  private $isPrimaryLine;

  /* @var string */
  private $announce;

  /* @var string */
  private $description;
  /** @var string */
  private $mediaImage;

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
  private $commentCount;
  /** @var int */
  private $price;
  /** @var int */
  private $priceAverage;
  /** @var int */
  private $priceOld;
  /** @var ProductStateEntity */
  private $state;
  /** @var ProductLineEntity */
  private $line;
  /** @var ProductMediaEntity[] */
  private $mediaList = array();
  /** @var ProductServiceEntity[] */
  private $serviceList = array();
  /** @var ProductKitEntity[] */
  private $kitList = array();
  /** @var int[] */
  private $relatedIdList = array();
  /** @var ProductEntity[] */
  private $relatedList = array();
  /** @var int[] */
  private $accessoryIdList = array();
  /** @var ProductEntity[] */
  private $accessoryList = array();
  /** @var ProductTagEntity[] */
  private $tagList = array();
  /** @var boolean */
  private $connectedProductsViewMode;


  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))              $this->id            = (int)$data['id'];
    if (array_key_exists('view_id', $data))         $this->viewId        = (int)$data['view_id'];
    if (array_key_exists('set_id', $data))          $this->setId         = (int)$data['set_id'];
    if (array_key_exists('is_model', $data))        $this->isModel       = (bool)$data['is_model'];
    if (array_key_exists('is_primary_line', $data)) $this->isPrimaryLine = (bool)$data['is_primary_line'];
    if (array_key_exists('model_id', $data))        $this->modelId       = (int)$data['model_id'];
    if (array_key_exists('score', $data))           $this->score         = (int)$data['score'];
    if (array_key_exists('name', $data))            $this->name          = (string)$data['name'];
    if (array_key_exists('link', $data))            $this->link          = (string)$data['link'];
    if (array_key_exists('token', $data))           $this->token         = (string)$data['token'];
    if (array_key_exists('name_web', $data))        $this->nameWeb       = (string)$data['name_web'];
    if (array_key_exists('prefix', $data))          $this->prefix        = (string)$data['prefix'];
    if (array_key_exists('article', $data))         $this->article       = (string)$data['article'];
    if (array_key_exists('bar_code', $data))        $this->barcode       = (string)$data['bar_code'];
    if (array_key_exists('tagline', $data))         $this->tagline       = (string)$data['tagline'];
    if (array_key_exists('announce', $data))        $this->announce      = (string)$data['announce'];
    if (array_key_exists('description', $data))     $this->description   = (string)$data['description'];
    if (array_key_exists('media_image', $data))     $this->mediaImage    = (string)$data['media_image'];
    if (array_key_exists('rating', $data))          $this->rating        = (int)$data['rating'];
    if (array_key_exists('rating_count', $data))    $this->ratingCount   = (int)$data['rating_count'];
    if (array_key_exists('comments_num', $data))    $this->commentCount  = (int)$data['comments_num'];
    if (array_key_exists('comment_count', $data))   $this->commentCount  = (int)$data['comment_count'];
    if (array_key_exists('price', $data))           $this->price         = $data['price'];
    if (array_key_exists('price_average', $data))   $this->priceAverage  = $data['price_average'];
    if (array_key_exists('price_old', $data))       $this->priceOld      = $data['price_old'];
    if (array_key_exists('connected_products_view_mode', $data))  $this->connectedProductsViewMode  = (int)$data['connected_products_view_mode'];


      //echo "<pre>", print_r($this,1), '</pre>';
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

    public function setConnectedProductsViewMode($connectedProductsViewMode)
    {
        $this->connectedProductsViewMode = $connectedProductsViewMode;
    }

    public function getConnectedProductsViewMode()
    {
        return $this->connectedProductsViewMode;
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

  public function setCategoryList(array $categoryList)
  {
    $this->categoryList = array();
    foreach ($categoryList as $category)
      $this->addCategory($category);
  }

  public function addCategory(ProductCategoryEntity $category)
  {
    $this->categoryList[] = $category;
  }

  /**
   * @return ProductCategoryEntity[]
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
    reset($this->categoryList);
    return current($this->categoryList);
  }

  /**
   * @return ProductCategoryEntity
   */
  public function getFinalCategory()
  {
    end($this->categoryList);
    return current($this->categoryList);
  }

  public function setAttributeList(array $attributeList)
  {
    $this->attributeList = array();
    $this->attributeMap = array();
    foreach ($attributeList as $attr)
      $this->addAttribute($attr);
  }

  public function addAttribute(ProductAttributeEntity $property)
  {
    $this->attributeList[] = $property;
    $this->attributeMap[$property->getId()] = $property;
  }

  /**
   * @return ProductAttributeEntity[]
   */
  public function getAttributeList()
  {
    return $this->attributeList;
  }

  /**
   * @param $id
   * @return ProductAttributeEntity
   */
  public function getAttribute($id)
  {
    if(isset($this->attributeMap[$id]))
      return $this->attributeMap[$id];
    else
      return null;
  }

  /**
   * Return ordered attribute list for listing.
   * Note!!! Contains manual sorting and filtering.
   *
   * @return ProductAttributeEntity[]
   */
  public function getAttributeListForListing()
  {
    $list = array();
    foreach ($this->attributeList as $attr)
      if ($attr->getIsViewList() && $attr->getStringValue())
        $list[] = $attr;
    usort($list, function(ProductAttributeEntity $a, ProductAttributeEntity $b)
    {
      return $a->getPosition() - $b->getPosition();
    });
    return $list;
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
   * @return string
   */
  public function getView()
  {
    return ($this->kitList)? "kit" : null;
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
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId = 1)
  {
    if ($this->mediaImage) {
      $urls = sfConfig::get('app_product_photo_url');
      return ProductMediaEntity::getHost().$urls[$viewId].$this->mediaImage;
    }
    else {
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
   * @param int $commentCount
   */
  public function setCommentCount($commentCount)
  {
    $this->commentCount = $commentCount;
  }

  /**
   * @return int
   */
  public function getCommentCount()
  {
    return $this->commentCount;
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

  /**
   * @return boolean
   */
  public function getIsModel()
  {
    return $this->isModel;
  }

  /**
   * @param \ProductModelEntity $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }

  /**
   * @return \ProductModelEntity
   */
  public function getModel()
  {
    return $this->model;
  }

  /**
   * @param \ProductStateEntity $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }

  /**
   * @return \ProductStateEntity
   */
  public function getState()
  {
    return $this->state;
  }

  public function getIsBuyable()
  {
    return $this->getState() && $this->getState()->getIsBuyable();
  }

  /**
   * @param \ProductLineEntity $line
   */
  public function setLine($line)
  {
    $this->line = $line;
  }

  /**
   * @return \ProductLineEntity
   */
  public function getLine()
  {
    return $this->line;
  }

  /**
   * @param ProductLabelEntity[] $labelList
   */
  public function setLabelList($labelList)
  {
    $this->labelList = array();
    foreach ($labelList as $label)
      $this->addLabel($label);
  }

  public function addLabel(ProductLabelEntity $label)
  {
    $this->labelList[] = $label;
  }

  /**
   * @return ProductLabelEntity[]
   */
  public function getLabelList()
  {
    return $this->labelList;
  }

  /**
   * @return ProductLabelEntity|null
   */
  public function getMainLabel()
  {
    return reset($this->labelList);
  }

  /**
   * @return bool
   */
  public function isInCart()
  {
    /** @var $user myUser */
    $user = sfContext::getInstance()->getUser();
    /** @var $cart UserCart */
    $cart = $user->getCart();
    return $cart->hasProduct($this->id);
  }

  /**
   * @return int
   */
  public function getCartQuantity()
  {
    /** @var $user myUser */
    $user = sfContext::getInstance()->getUser();
    /** @var $cart UserCart */
    $cart = $user->getCart();
    return $cart->getQuantityById($this->id);
  }

  /**
   * @param ProductMediaEntity[] $media
   */
  public function setMediaList(array $media)
  {
    $this->mediaList = array();
    foreach($media as $item){
      assert($item instanceof ProductMediaEntity);
      $this->mediaList[] = $item;
    }
  }

  public function addMedia(ProductMediaEntity $media)
  {
    $this->mediaList[] = $media;
  }

  /**
   * @return ProductMediaEntity[]
   */
  public function getMediaList()
  {
    return $this->mediaList;
  }

  /**
   * @return ProductMediaEntity[]
   */
  public function getPhotoList()
  {
    $list = array();
    foreach($this->mediaList as $image)
      if($image->getTypeId() == ProductMediaEntity::TYPE_IMAGE)
        $list[] = $image;
    return $list;
  }

  /**
   * @return ProductMediaEntity[]
   */
  public function getPhoto3dList()
  {
    $list = array();
    foreach($this->mediaList as $image)
      if($image->getTypeId() == ProductMediaEntity::TYPE_3D)
        $list[] = $image;
    return $list;
  }

  /**
   * @param ProductServiceEntity[] $serviceList
   */
  public function setServiceList($serviceList)
  {
    $this->serviceList = array();
    foreach($serviceList as $service)
    {
      assert($service instanceof ProductServiceEntity);
      $this->serviceList[] = $service;
    }
  }

  /**
   * @param $service
   */
  public function addService(ProductServiceEntity $service)
  {
    $this->serviceList[] = $service;
  }

  /**
   * @return ProductServiceEntity[]
   */
  public function getServiceList()
  {
    return $this->serviceList;
  }

  /**
   * @param ProductKitEntity[] $kitList
   */
  public function setKitList($kitList)
  {
    $this->kitList = array();
    foreach($kitList as $kit)
    {
      assert($kit instanceof ProductKitEntity);
      $this->kitList[] = $kit;
    }
  }

  /**
   * @param ProductKitEntity $kit
   */
  public function addKit(ProductKitEntity $kit)
  {
    $this->kitList[] = $kit;
  }

  /**
   * @return ProductKitEntity[]
   */
  public function getKitList()
  {
    return $this->kitList;
  }

  /**
   * @param int[]$accessoryIdList
   */
  public function setAccessoryIdList(array $accessoryIdList)
  {
    $this->accessoryIdList = $accessoryIdList;
  }

  /**
   * @return int[]
   */
  public function getAccessoryIdList()
  {
    return $this->accessoryIdList;
  }

  /**
   * @param int[] $relatedIdList
   */
  public function setRelatedIdList(array $relatedIdList)
  {
    $this->relatedIdList = $relatedIdList;
  }

  /**
   * @return int[]
   */
  public function getRelatedIdList()
  {
    return $this->relatedIdList;
  }

  public function haveToShowAveragePrice()
  {
    return ($this->hasSaleLabel() && $this->price > 0 && $this->price < $this->priceAverage);
  }

  public function haveToShowOldPrice()
  {
    $hasLabel = false;
    foreach ($this->labelList as $label)
    {
      if (in_array($label->getId(), array(ProductLabelEntity::LABEL_SALE, ProductLabelEntity::LABEL_SUPER_PRICE, ProductLabelEntity::LABEL_ACTION, ProductLabelEntity::LABEL_DISCOUNT, ProductLabelEntity::LABEL_FANS)))
      {
        $hasLabel = true;
        break;
      }
    }

    return ($hasLabel && $this->price > 0 && $this->price < $this->priceOld && ($this->price / $this->priceOld <= 0.95));
  }

  public function hasSaleLabel()
  {
    foreach ($this->labelList as $label)
      if ($label->isSaleLabel())
        return true;
    return false;
  }

  /**
   * @param int $priceAverage
   */
  public function setPriceAverage($priceAverage)
  {
    $this->priceAverage = $priceAverage;
  }

  /**
   * @return int
   */
  public function getPriceAverage()
  {
    return $this->priceAverage;
  }

  /**
   * @param int $priceAverage
   */
  public function setPriceOld($priceOld)
  {
    $this->priceOld = $priceOld;
  }

  /**
   * @return int
   */
  public function getPriceOld()
  {
    return $this->priceOld;
  }

  /**
   * @return string
   */
  public function getPath()
  {
    return str_replace('/product/', '', $this->link);
  }

  public function setAccessoryList($accessoryList)
  {
    $this->accessoryList = $accessoryList;
  }

  public function getAccessoryList()
  {
    return $this->accessoryList;
  }

  public function setRelatedList($relatedList)
  {
    $this->relatedList = $relatedList;
  }

  public function getRelatedList()
  {
    return $this->relatedList;
  }

  /**
   * @return ProductServiceEntity[]
   */
  public function getServiceListInCart()
  {
    $idList = array();
    foreach(sfContext::getInstance()->getUser()->getCart()->getServices() as $serviceId => $serviceInCart)
      if(isset($serviceInCart[$this->id]))
        $idList[] = $serviceId;

    $list = array();
    foreach($this->serviceList as $service)
      if(in_array($service->getId(), $idList))
        $list[] = $service;
    return $list;
  }

  /**
   * @param \ProductPropertyGroupEntity[] $list
   */
  public function setPropertyGroupList(array $list)
  {
    $this->propertyGroupList = array();
    foreach($list as $group){
      $this->addPropertyGroup($group);
    }
  }

  /**
   * @param \ProductPropertyGroupEntity $group
   */
  public function addPropertyGroup(ProductPropertyGroupEntity $group)
  {
    $this->propertyGroupList[] = $group;
  }

  /**
   * @return \ProductPropertyGroupEntity[]
   */
  public function getPropertyGroupList()
  {
    return $this->propertyGroupList;
  }

  /**
   * @param ProductTagEntity[] $tagList
   */
  public function setTagList($tagList)
  {
    $this->tagList = array();
    foreach($tagList as $tag)
      $this->addTag($tag);
  }

  public function addTag(ProductTagEntity $tag)
  {
    $this->tagList[] = $tag;
  }

  /**
   * @return ProductTagEntity[]
   */
  public function getTagList()
  {
    return $this->tagList;
  }
}