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

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))       $this->id       = (int)$data['id'];
    if (array_key_exists('name', $data))     $this->name     = (string)$data['name'];
    if (array_key_exists('link', $data))     $this->link     = (string)$data['link'];
    if (array_key_exists('token', $data))    $this->token    = (string)$data['token'];
    if (array_key_exists('position', $data)) $this->position = (int)$data['position'];
    if (array_key_exists('is_shown_in_menu', $data)) $this->isShownInMenu = (bool)$data['is_shown_in_menu'];
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
