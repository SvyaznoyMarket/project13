<?php

/**
 * @deprecated
 */
class ProductServiceEntity
{
  const MIN_SERVICE_BUY_PRICE = 950;

  /** @var int */
  private $id;
  /** @var string */
  private $name;
  /** @var string */
  private $token;
  /** @var float */
  private $price;
  /** @var boolean */
  private $is_in_shop;
  /** @var boolean */
  private $is_delivery;
  /** @var boolean */
  private $onlyInShop;

  /**
   * @param array $data
   */
  function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))            $this->id    = (int)$data['id'];
    if (array_key_exists('name', $data))          $this->name  = (string)$data['name'];
    if (array_key_exists('token', $data))         $this->token = (string)$data['token'];
    if (array_key_exists('price', $data))         $this->price = $data['price'];
    if (array_key_exists('is_in_shop', $data))    $this->is_in_shop       = (bool)$data['is_in_shop'];
    if (array_key_exists('is_delivery', $data))   $this->is_delivery      = (bool)$data['is_delivery'];
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
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = (float)$price;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
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

  public function getSiteToken()
  {
    $cache = myCache::getInstance();
    $key = __CLASS__.':id'.$this->id;
    if(!($token = $cache->get($key))){
      /** @var $service Service */
      $service = ServiceTable::getInstance()->getByCoreId($this->id);
      $token = $service->token;
      $cache->set($key, $token);
      $cache->addTag('tag-'.$service->id, $key);
    }
    return $token;
  }

  /**
   * @param boolean $onlyInShop
   */
  public function setOnlyInShop($onlyInShop)
  {
    $this->onlyInShop = $onlyInShop;
  }

  /**
   * @return boolean
   */
  public function getOnlyInShop()
  {
    return !$this->is_delivery && $this->is_in_shop;
  }

  /**
   * @return bo
   */
  public function isInSale()
  {
    $region = sfContext::getInstance()->getUser()->getRegion();

    return $region['has_f1'] && !$this->onlyInShop && $this->price && $this->price >= self::MIN_SERVICE_BUY_PRICE;
  }

  /**
   * @param boolean $is_delivery
   */
  public function setIsDelivery($is_delivery)
  {
    $this->is_delivery = $is_delivery;
  }

  /**
   * @return boolean
   */
  public function getIsDelivery()
  {
    return $this->is_delivery;
  }

  /**
   * @param boolean $is_in_shop
   */
  public function setIsInShop($is_in_shop)
  {
    $this->is_in_shop = $is_in_shop;
  }

  /**
   * @return boolean
   */
  public function getIsInShop()
  {
    return $this->is_in_shop;
  }
}
