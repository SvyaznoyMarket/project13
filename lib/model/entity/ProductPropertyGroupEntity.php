<?php

class ProductPropertyGroupEntity
{
  private $id;
  private $name;
  private $position;
  /** @var array */
  private $attributeList = array();

  public function __construct(array $data = array())
  {
    if(array_key_exists('id', $data)) $this->id = (int)$data['id'];
    if(array_key_exists('name', $data)) $this->name = (string)$data['name'];
    if(array_key_exists('position', $data)) $this->position = (int)$data['position'];
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setPosition($position)
  {
    $this->position = $position;
  }

  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param ProductAttributeEntity[] $list
   */
  public function setAttributeList($list)
  {
    $this->attributeList = array();
    foreach($list as $attr)
      $this->addAttribute($attr);
  }

  public function addAttribute(ProductAttributeEntity $attr)
  {
    $this->attributeList[] = $attr;
  }

  /**
   * @return ProductAttributeEntity[]
   */
  public function getAttributeList()
  {
    return $this->attributeList;
  }
}
