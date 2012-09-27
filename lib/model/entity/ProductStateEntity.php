<?php

class ProductStateEntity
{
  /** @var int */
  private $statusId;
  /** @var boolean */
  private $isImage;
  /** @var boolean */
  private $isPrice;
  /** @var boolean */
  private $isShop;
  /** @var boolean */
  private $isStore;
  /** @var boolean */
  private $isSupplier;
  /** @var boolean */
  private $isViewList;
  /** @var boolean */
  private $isViewCard;
  /** @var boolean */
  private $isBuyable;

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    if (array_key_exists('status_id', $data))     $this->statusId    = (int)$data['status_id'];
    if (array_key_exists('is_image', $data))      $this->isImage     = (bool)$data['is_image'];
    if (array_key_exists('is_price', $data))      $this->isPrice     = (bool)$data['is_price'];
    if (array_key_exists('is_shop', $data))       $this->isShop      = (bool)$data['is_shop'];
    if (array_key_exists('is_store', $data))      $this->isStore     = (bool)$data['is_store'];
    if (array_key_exists('is_supplier', $data))   $this->isSupplier  = (bool)$data['is_supplier'];
    if (array_key_exists('is_view_list', $data))  $this->isViewList  = (bool)$data['is_view_list'];
    if (array_key_exists('is_view_card', $data))  $this->isViewCard  = (bool)$data['is_view_card'];
    if (array_key_exists('is_buyable', $data))    $this->isBuyable   = (bool)$data['is_buyable'];
    if (array_key_exists('is_quick_only', $data)) $this->isQuickOnly = (bool)$data['is_quick_only'];
  }

  /**
   * @param boolean $is_buyable
   */
  public function setIsBuyable($is_buyable)
  {
    $this->isBuyable = (boolean)$is_buyable;
  }

  /**
   * @return boolean
   */
  public function getIsBuyable()
  {
    return $this->isBuyable;
  }

  /**
   * @param boolean $is_buyable
   */
  public function setIsQuickOnly($is_quick_only)
  {
    $this->isQuickOnly= (boolean)$is_quick_only;
  }

  /**
   * @return boolean
   */
  public function getIsQuickOnly()
  {
    return $this->isQuickOnly;
  }

  /**
   * @param boolean $is_image
   */
  public function setIsImage($is_image)
  {
    $this->isImage = (boolean)$is_image;
  }

  /**
   * @return boolean
   */
  public function getIsImage()
  {
    return $this->isImage;
  }

  /**
   * @param boolean $is_price
   */
  public function setIsPrice($is_price)
  {
    $this->isPrice = (boolean)$is_price;
  }

  /**
   * @return boolean
   */
  public function getIsPrice()
  {
    return $this->isPrice;
  }

  /**
   * @param boolean $is_shop
   */
  public function setIsShop($is_shop)
  {
    $this->isShop = (boolean)$is_shop;
  }

  /**
   * @return boolean
   */
  public function getIsShop()
  {
    return $this->isShop;
  }

  /**
   * @param boolean $is_store
   */
  public function setIsStore($is_store)
  {
    $this->isStore = (boolean)$is_store;
  }

  /**
   * @return boolean
   */
  public function getIsStore()
  {
    return $this->isStore;
  }

  /**
   * @param boolean $is_supplier
   */
  public function setIsSupplier($is_supplier)
  {
    $this->isSupplier = (boolean)$is_supplier;
  }

  /**
   * @return boolean
   */
  public function getIsSupplier()
  {
    return $this->isSupplier;
  }

  /**
   * @param boolean $is_view_card
   */
  public function setIsViewCard($is_view_card)
  {
    $this->isViewCard = (boolean)$is_view_card;
  }

  /**
   * @return boolean
   */
  public function getIsViewCard()
  {
    return $this->isViewCard;
  }

  /**
   * @param boolean $is_view_list
   */
  public function setIsViewList($is_view_list)
  {
    $this->isViewList = (boolean)$is_view_list;
  }

  /**
   * @return boolean
   */
  public function getIsViewList()
  {
    return $this->isViewList;
  }

  /**
   * @param int $status_id
   */
  public function setStatusId($status_id)
  {
    $this->statusId = (int)$status_id;
  }

  /**
   * @return int
   */
  public function getStatusId()
  {
    return $this->statusId;
  }
}
