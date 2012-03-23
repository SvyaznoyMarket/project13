<?php

/**
 * Опция у свойства товара
 */
class ProductPropertyOptionEntity
{
  /* @var int */
  private $id;

  /* @var string */
  private $name;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data)) $this->setId($data['id']);
    if (array_key_exists('name', $data)) $this->setName($data['name']);
    if (array_key_exists('value', $data)) $this->setName($data['value']);
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
   * @param string $name
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