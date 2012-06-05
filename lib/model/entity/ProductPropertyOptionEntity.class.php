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

  /** @var ProductEntity */
  private $product;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))    $this->id   = (int)$data['id'];
    if (array_key_exists('name', $data))  $this->name = (string)$data['name'];
    if (array_key_exists('value', $data)) $this->name = (string)$data['value'];
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

  public function getHumanizedName()
  {
    if (in_array($this->name, array('false', false), true)) {
      return 'нет';
    }
    if (in_array($this->name, array('true', true), true)) {
      return 'да';
    }
    return $this->name;
  }

  /**
   * @param \ProductEntity $product
   */
  public function setProduct(ProductEntity $product)
  {
    $this->product = $product;
  }

  /**
   * @return \ProductEntity
   */
  public function getProduct()
  {
    return $this->product;
  }
}