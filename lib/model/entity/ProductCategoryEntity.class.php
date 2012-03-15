<?php

/**
 * Категория товара
 */
class ProductCategoryEntity
{
  /* @var integer */
  private $id;

  /* @var ProductCategoryEntity|null */
  private $parent = null;

  /* @var string */
  private $name;

  /* @var boolean */
  private $isActive;

  /* @var boolean */
  private $hasLine;

  /* @var boolean */
  private $isFurniture;

  /* @var integer */
  private $level;

  /* @var integer */
  private $lft;

  /* @var integer */
  private $rgt;

  /* @var integer */
  private $position;

  /* @var string */
  private $link;

  /* @var string */
  private $image;

  /* @var ProductCategoryEntity[] */
  private $child;


  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setHasLine($hasLine)
  {
    $this->hasLine = $hasLine;
  }

  public function hasLine()
  {
    return $this->hasLine;
  }

  public function setImage($image)
  {
    $this->image = $image;
  }

  public function getImage()
  {
    return $this->image;
  }

  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }

  public function isActive()
  {
    return $this->isActive;
  }

  public function setIsFurniture($isFurniture)
  {
    $this->isFurniture = $isFurniture;
  }

  public function isFurniture()
  {
    return $this->isFurniture;
  }

  public function setLevel($level)
  {
    $this->level = $level;
  }

  public function getLevel()
  {
    return $this->level;
  }

  public function setLft($lft)
  {
    $this->lft = $lft;
  }

  public function getLft()
  {
    return $this->lft;
  }

  public function setLink($link)
  {
    $this->link = $link;
  }

  public function getLink()
  {
    return $this->link;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setParent(ProductCategoryEntity $parent)
  {
    $this->parent = $parent;
  }

  public function getParent()
  {
    return $this->parent;
  }

  public function setPosition($position)
  {
    $this->position = $position;
  }

  public function getPosition()
  {
    return $this->position;
  }

  public function setRgt($rgt)
  {
    $this->rgt = $rgt;
  }

  public function getRgt()
  {
    return $this->rgt;
  }

  public function setChild(array $child)
  {
    $this->child = $child;
  }

  public function addChild($child)
  {
    $this->child[] = $child;
  }

  public function getChild()
  {
    return $this->child;
  }
}