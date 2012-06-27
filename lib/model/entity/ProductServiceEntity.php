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
    if (array_key_exists('only_in_shop', $data))  $this->onlyInShop = (boolean)$data['only_in_shop'];
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
    return $this->onlyInShop;
  }

  /**
   * @return bo
   */
  public function isInSale()
  {
    return !$this->onlyInShop && $this->price && $this->price >= self::MIN_SERVICE_BUY_PRICE;
  }
}
