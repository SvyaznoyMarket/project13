<?php

/**
 * Регион
 */
class RegionEntity
{
  /* @var integer */
  private $id;

  /* @var boolean */
  private $isActive;

  /* @var integer */
  private $level;

  /* @var integer */
  private $lft;

  /* @var integer */
  private $rgt;

  /* @var ProductCategoryEntity|null */
  private $parent = null;

  /* @var ProductCategoryEntity[] */
  private $child;

  /* @var PriceTypeEntity|null */
  private $priceType = null;


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
   * @param boolean $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }

  /**
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->isActive;
  }

  /**
   * @param int $level
   */
  public function setLevel($level)
  {
    $this->level = $level;
  }

  /**
   * @return int
   */
  public function getLevel()
  {
    return $this->level;
  }

  /**
   * @param int $lft
   */
  public function setLft($lft)
  {
    $this->lft = $lft;
  }

  /**
   * @return int
   */
  public function getLft()
  {
    return $this->lft;
  }

  /**
   * @param int $rgt
   */
  public function setRgt($rgt)
  {
    $this->rgt = $rgt;
  }

  /**
   * @return int
   */
  public function getRgt()
  {
    return $this->rgt;
  }

  /**
   * @param null|ProductCategoryEntity $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }

  /**
   * @return null|ProductCategoryEntity
   */
  public function getParent()
  {
    return $this->parent;
  }

  public function setChild($child)
  {
    $this->child = $child;
  }

  public function getChild()
  {
    return $this->child;
  }

  /**
   * @param PriceTypeEntity|null $priceType
   */
  public function setPriceType($priceType)
  {
    $this->priceType = $priceType;
  }

  /**
   * @return PriceTypeEntity|null
   */
  public function getPriceType()
  {
    return $this->priceType;
  }
}
