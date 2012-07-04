<?php

/**
 * Элемент промо
 */
class PromoItemEntity
{
  const TYPE_PRODUCT = 1;
  const TYPE_SERVICE = 2;
  const TYPE_PRODUCT_CATEGORY = 3;

  /* @var integer */
  private $id;

  /** @var integer */
  private $type;

  /** @var ProductEntity|ProductCategoryEntity */
  private $object = null;


  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))      $this->id   = (int)$data['id'];
    if (array_key_exists('type_id', $data)) $this->type = (int)$data['type_id'];
    if (array_key_exists('object', $data)) $this->object = $data['object'];
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
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }

  /**
   * @return int
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param ProductCategoryEntity|ProductEntity $object
   */
  public function setObject($object)
  {
    $this->object = $object;
  }

  /**
   * @return ProductCategoryEntity|ProductEntity
   */
  public function getObject()
  {
    return $this->object;
  }
}
