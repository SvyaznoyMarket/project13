<?php

/**
 * Тип товара
 */
class ProductTypeEntity
{
  /* @var integer */
  private $id;

  /* @var string */
  private $name;


  public function __construct(array $data = array()){
    if(array_key_exists('id', $data)) $this->setId($data['id']);
    if(array_key_exists('name', $data)) $this->setId($data['name']);
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
