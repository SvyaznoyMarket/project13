<?php

/**
 * Метка товара
 */
class ProductLabelEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $image;

  /* @var string */
  private $name;
  /** @var int */
  private $priority;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data)) $this->setId($data['id']);
    if (array_key_exists('image', $data)) $this->setImage($data['image']);
    if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
  }

  /**
   * @param int $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }

  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
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

  /**
   * @param int $size
   * @return null|string
   */
  public function getImageUrl($size = 0)
  {
    if ($this->image) {
      $urls = sfConfig::get('app_product_label_photo_url');
      return $urls[$size] . $this->image;
    }
    else {
      return null;
    }
  }

  /**
   * @param int $priority
   */
  public function setPriority($priority)
  {
    $this->priority = $priority;
  }

  /**
   * @return int
   */
  public function getPriority()
  {
    return $this->priority;
  }
}