<?php

class ProductLineEntity
{
  /** @var int */
  private $id;
  /** @var string */
  private $name;
  /** @var string */
  private $mediaImage;
  /** @var int */
  private $productCount;

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))                $this->id           = (int)$data['id'];
    if (array_key_exists('name', $data))              $this->name         = (string)$data['name'];
    if (array_key_exists('media_image', $data))       $this->mediaImage   = (string)$data['media_image'];
    if (array_key_exists('count', $data))             $this->productCount = (int)$data['count'];
    if (array_key_exists('product_count', $data))     $this->productCount = (int)$data['product_count'];
    if (array_key_exists('products_quantity', $data)) $this->productCount = (int)$data['products_quantity'];
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
   * @param $media_image
   */
  public function setMediaImage($media_image)
  {
    $this->mediaImage = (string)$media_image;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
  }

  /**
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId = 1)
  {
    if ($this->mediaImage) {
      $urls = sfConfig::get('app_product_photo_url');
      return $urls[$viewId] . $this->mediaImage;
    }
    else {
      return null;
    }
  }

  /**
   * @param $name
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
   * @return int
   */
  public function getProductCount()
  {
    return $this->productCount;
  }

  /**
   * @param int $productCount
   */
  public function setProductCount($productCount)
  {
    $this->productCount = (int)$productCount;
  }

  // @todo move to Soa data loading
  public function getLink()
  {
    /** @var $cache myRedisCache */
    $cache = myCache::getInstance();
    $key = __CLASS__.':id'.$this->id;
    if(!($token = $cache->get($key))){
      /** @var $line ProductLine */
      $line = ProductLineTable::getInstance()->getByCoreId($this->id);
      $token = $line->token;
      $cache->set($key, $token);
      $cache->addTag('productLine-'.$line->id, $key);
    }
    return sfContext::getInstance()->getRouting()->generate('lineCard', array('line' => $token));
  }
}
