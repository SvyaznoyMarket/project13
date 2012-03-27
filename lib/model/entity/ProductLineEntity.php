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
    if (isset($data['id'])) $this->setId($data['id']);
    if (isset($data['name'])) $this->setName($data['name']);
    if (isset($data['media_image'])) $this->setMediaImage($data['media_image']);
    if (isset($data['product_count'])) $this->setProductCount($data['product_count']);
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
}
