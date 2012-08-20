<?php
namespace light;
/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 15.05.12
 * Time: 14:35
 * To change this template use File | Settings | File Templates.
 */
class CategoryShortData
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
   * @var string
   */
  private $link;

  /**
   * @var string
   */
  private $token;

  /**
   * @var int
   */
  private $position;

  private $isShownInMenu;

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
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
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
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
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

  public function setIsShownInMenu($isShownInMenu)
  {
      $this->isShownInMenu = $isShownInMenu;
  }

  public function getIsShownInMenu()
  {
      return (bool)$this->isShownInMenu;
  }
}
