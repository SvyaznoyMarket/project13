<?php

/**
 * Тип цены
 */
class PriceTypeEntity
{
  /* @var integer */
  private $id;

  /* @var boolean */
  private $isActive;

  /* @var boolean */
  private $isPrimary;

  /* @var string */
  private $name;


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
   * @param boolean $isPrimary
   */
  public function setIsPrimary($isPrimary)
  {
    $this->isPrimary = $isPrimary;
  }

  /**
   * @return boolean
   */
  public function getIsPrimary()
  {
    return $this->isPrimary;
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
