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

  public function __construct(array $data = array()){
    if(array_key_exists('id', $data))         $this->setId($data['id']);
    if(array_key_exists('is_active', $data))  $this->setIsActive($data['is_active']);
    if(array_key_exists('is_primary', $data)) $this->setIsPrimary($data['is_primary']);
    if(array_key_exists('name', $data))       $this->setName($data['name']);
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
