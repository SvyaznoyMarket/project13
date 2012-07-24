<?php

class ServiceEntity
{
  /** @var int */
  private $id;
  /** @var string */
  private $name;
  /** @var string */
  private $token;
  /** @var string */
  private $description;
  /** @var string */
  private $work;
  /** @var string */
  private $media_image;
  /** @var boolean */
  private $is_in_shop;
  /** @var boolean */
  private $is_delivery;
  /** @var int */
  private $price_type_id;
  /** @var float|null */
  private $price;
  /** @var int|null */
  private $price_percent;
  /** @var float|null */
  private $price_min;
  /** @var ServiceCategoryEntity[] */
  private $category_list = array();
  /** @var int[] */
  private $alike_id_list = array();
  /** @var ServiceEntity[] */
  private $alike_list = array();

  public function __construct(array $data = array())
  {
    if(array_key_exists('id', $data))               $this->id               = (int)$data['id'];
    if(array_key_exists('name', $data))             $this->name             = (string)$data['name'];
    if(array_key_exists('token', $data))            $this->token            = (string)$data['token'];
    if(array_key_exists('description', $data))      $this->description      = (string)$data['description'];
    if(array_key_exists('work', $data))             $this->work             = (string)$data['work'];
    if(array_key_exists('media_image', $data))      $this->media_image      = (string)$data['media_image'];
    if(array_key_exists('is_in_shop', $data))       $this->is_in_shop       = (bool)$data['is_in_shop'];
    if(array_key_exists('is_delivery', $data))      $this->is_delivery      = (bool)$data['is_delivery'];
    if(array_key_exists('price_type_id', $data))    $this->price_type_id    = (int)$data['price_type_id'];
    if(array_key_exists('price', $data) && !is_null($data['price'])){
      $this->price = (float)$data['price'];
      if($this->price === 0.01){
        $this->price = 0.0;
      }
    }
    if(array_key_exists('price_percent', $data) && !is_null($data['price_percent'])){
      $this->price_percent = (int)$data['price_percent'];
    }
    if(array_key_exists('price_min', $data) && !is_null($data['price_min'])){
      $this->price_min = (float)$data['price_min'];
      if($this->price_min === 0.01){
        $this->price_min = 0.0;
      }
    }
  }

  /**
   * @param int[] $alike_id_list
   */
  public function setAlikeIdList(array $alike_id_list)
  {
    $this->alike_id_list = array();
    foreach($alike_id_list as $alike_id)
      $this->addAlikeId($alike_id);
  }

  /**
   * @param int $id
   */
  public function addAlikeId($id)
  {
    $this->alike_id_list[] = (int)$id;
  }

  /**
   * @return int[]
   */
  public function getAlikeIdList()
  {
    return $this->alike_id_list;
  }

  /**
   * @param ServiceEntity[] $alike_list
   */
  public function setAlikeList($alike_list)
  {
    $this->alike_list = array();
    foreach($alike_list as $alike)
      $this->addAlike($alike);
  }

  /**
   * @param ServiceEntity $entity
   */
  public function addAlike(ServiceEntity $entity)
  {
    $this->alike_list[] = $entity;
  }

  /**
   * @return ServiceEntity[]
   */
  public function getAlikeList()
  {
    return $this->alike_list;
  }

  /**
   * @param ServiceCategoryEntity[] $category_list
   */
  public function setCategoryList($category_list)
  {
    $this->category_list = array();
    foreach($category_list as $category)
      $this->addCategory($category);
  }

  /**
   * @param ServiceCategoryEntity $entity
   */
  public function addCategory(ServiceCategoryEntity $entity)
  {
    $this->category_list[] = $entity;
  }

  /**
   * @return ServiceCategoryEntity[]
   */
  public function getCategoryList()
  {
    return $this->category_list;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = (string)$description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = (int)$id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param boolean $is_only_in_shop
   */
  public function setIsInShop($is_only_in_shop)
  {
    $this->is_in_shop = (bool)$is_only_in_shop;
  }

  /**
   * @return boolean
   */
  public function getIsInShop()
  {
    return $this->is_in_shop;
  }

  /**
   * @param boolean $is_delivery
   */
  public function setIsDelivery($is_delivery)
  {
    $this->is_delivery = (bool)$is_delivery;
  }

  /**
   * @return boolean
   */
  public function getIsDelivery()
  {
    return $this->is_delivery;
  }

  /**
   * @param string $media_image
   */
  public function setMediaImage($media_image)
  {
    $this->media_image = (string)$media_image;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->media_image;
  }

  /**
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId = 1)
  {
    if ($this->media_image) {
      $urls = sfConfig::get('app_service_photo_url');
      return $urls[$viewId] . $this->media_image;
    }
    else {
      return null;
    }
  }

  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = (string)$name;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param float|null $price
   */
  public function setPrice($price)
  {
    $this->price = (float)$price;
  }

  /**
   * @return float|null
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @param float|null $price_min
   */
  public function setPriceMin($price_min)
  {
    $this->price_min = (float)$price_min;
  }

  /**
   * @return float|null
   */
  public function getPriceMin()
  {
    return $this->price_min;
  }

  /**
   * @param int $price_percent
   */
  public function setPricePercent($price_percent)
  {
    $this->price_percent = (int)$price_percent;
  }

  /**
   * @return int|null
   */
  public function getPricePercent()
  {
    return $this->price_percent;
  }

  /**
   * @param int $price_type_id
   */
  public function setPriceTypeId($price_type_id)
  {
    $this->price_type_id = (int)$price_type_id;
  }

  /**
   * @return int
   */
  public function getPriceTypeId()
  {
    return $this->price_type_id;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = (string)$token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * @param string $work
   */
  public function setWork($work)
  {
    $this->work = (string)$work;
  }

  /**
   * @return string
   */
  public function getWork()
  {
    return $this->work;
  }

  public function getNavigation()
  {
    $list = array();
    $list[] = array(
      'name' => 'F1 Сервис',
      'url' => url_for('service_index'),
    );
    foreach($this->category_list as $category){
      $list[] = array(
        'name' => $category->getName(),
        'url' => $category->getLink(),
      );
    }
    $list[] = array(
      'name' => $this->name,
      'url' => url_for('service_show', array('service'=>$this->token)),
    );
    return $list;
  }
}
