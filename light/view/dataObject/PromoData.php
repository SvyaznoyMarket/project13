<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 30.05.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
class PromoData
{

  /**
   * @var int
   */
  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @var int
   */
  private $typeId;

  /**
   * @var string
   */
  private $url;

  /**
   * @var string
   */
  private $img;

  /**
   * @var int
   */
  private $position;

  /**
   * @var string
   */
  private $start;

  /**
   * @var string
   */
  private $finish;

  /**
   * @var array
   */
  private $itemList;

  /**
   * @param string $finish
   */
  public function setFinish($finish)
  {
    $this->finish = $finish;
  }

  /**
   * @return string
   */
  public function getFinish()
  {
    return $this->finish;
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
   * @param string $img
   */
  public function setImg($img)
  {
    $this->img = $img;
  }

  /**
   * @return string
   */
  public function getImg()
  {
    return $this->img;
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

  /**
   * @param int $position
   */
  public function setPosition($position)
  {
    $this->position = $position;
  }

  /**
   * @return int
   */
  public function getPosition()
  {
    return $this->position;
  }

  /**
   * @param string $start
   */
  public function setStart($start)
  {
    $this->start = $start;
  }

  /**
   * @return string
   */
  public function getStart()
  {
    return $this->start;
  }

  /**
   * @param int $typeId
   */
  public function setTypeId($typeId)
  {
    $this->typeId = $typeId;
  }

  /**
   * @return int 1-banner, 2-dummy, 3-exclusive
   */
  public function getTypeId()
  {
    return $this->typeId;
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @param array $itemList
   */
  public function setItemList($itemList)
  {
    $this->itemList = $itemList;
  }

  /**
   * @return array
   */
  public function getItemList()
  {
    return $this->itemList;
  }

  /**
   * @return bool
   */
  public function hasItems()
  {
    return (count($this->itemList) > 0);
  }
}
