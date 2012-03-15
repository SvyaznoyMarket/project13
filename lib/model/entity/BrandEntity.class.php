<?php

/**
 * Бренд
 */
class BrandEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $image;

  /* @var string */
  private $description;


  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setImage($image)
  {
    $this->image = $image;
  }

  public function getImage()
  {
    return $this->image;
  }
}
