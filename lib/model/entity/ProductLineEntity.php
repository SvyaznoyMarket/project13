<?php

class ProductLineEntity
{
  /** @var int */
  private $id;
  /** @var string */
  private $name;
  /** @var string */
  private $mediaImage;

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    if (isset($data['id'])) $this->setId($data['id']);
    if (isset($data['name'])) $this->setName($data['name']);
    if (isset($data['media_image'])) $this->setMediaImage($data['media_image']);
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
}
