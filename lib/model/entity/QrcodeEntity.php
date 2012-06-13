<?php

class QrcodeEntity
{
  /** @var string */
  private $hash;
  /** @var QrcodeItemEntity[] */
  private $itemList = array();

  public function __construct(array $data = array()){
    if(array_key_exists('hash', $data)) $this->hash = (string)$data['hash'];
  }

  /**
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = (string)$hash;
  }

  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }

  /**
   * @param App_Model_V2_Qrcode_ItemEntity[] $itemList
   */
  public function setItemList($itemList)
  {
    $this->itemList = array();
    foreach ($itemList as $item)
      $this->addItem($item);
  }

  /**
   * @param QrcodeItemEntity $item
   */
  public function addItem(QrcodeItemEntity $item)
  {
    $this->itemList[] = $item;
  }

  /**
   * @return QrcodeItemEntity[]
   */
  public function getItemList()
  {
    return $this->itemList;
  }
}

class QrcodeItemEntity
{
  const PRODUCT_TYPE = 1;

  /** @var int */
  private $typeId;
  /** @var int */
  private $itemId;

  public function __construct(array $data = array()){
    if(array_key_exists('type_id', $data)) $this->typeId = (string)$data['type_id'];
    if(array_key_exists('item_id', $data)) $this->itemId = (string)$data['item_id'];
  }
  /**
   * @param int $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = (int)$itemId;
  }

  /**
   * @return int
   */
  public function getItemId()
  {
    return $this->itemId;
  }

  /**
   * @param int $typeId
   */
  public function setTypeId($typeId)
  {
    $this->typeId = (int)$typeId;
  }

  /**
   * @return int
   */
  public function getTypeId()
  {
    return $this->typeId;
  }

  /**
   * @return bool
   */
  public function isProduct()
  {
    return $this->typeId == self::PRODUCT_TYPE;
  }
}