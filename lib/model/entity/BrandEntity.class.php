<?php

/**
 * Бренд
 */
class BrandEntity
{
  /* @var integer */
  private $id;
  /** @var string */
  private $name;
  /* @var string */
  private $image;
  /* @var string */
  private $description;
  /** @var boolean */
  private $isViewFilter;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data)) $this->setId($data['id']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
    if (array_key_exists('media_image', $data)) $this->setImage($data['media_image']);
    if (array_key_exists('description', $data)) $this->setDescription($data['description']);
    if (array_key_exists('is_view_filter', $data)) $this->setIsViewFilter($data['is_view_filter']);
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
   * @param string$image
   */
  public function setImage($image)
  {
    $this->image = (string)$image;
  }

  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * @param boolean $isViewFilter
   */
  public function setIsViewFilter($isViewFilter)
  {
    $this->isViewFilter = $isViewFilter;
  }

  /**
   * @return boolean
   */
  public function getIsViewFilter()
  {
    return $this->isViewFilter;
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
}
