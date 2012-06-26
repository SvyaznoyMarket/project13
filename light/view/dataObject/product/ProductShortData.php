<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 13.06.12
 * Time: 20:57
 * To change this template use File | Settings | File Templates.
 */
class ProductShortData
{
  /** @var int */
  private $id;

  /** @var string */
  private $name;

  /** @var string */
  private $token;

  /** @var string */
  private $link;

  /** @var float */
  private $price;

  /** @var string */
  private $article;

  /** @var string */
  private $barcode;

  /** @var string */
  private $announce;

  /** @var string */
  private $description;

  /** @var string */
  private $mediaImage;


  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))              $this->id            = (int)$data['id'];
    if (array_key_exists('name', $data))            $this->name          = (string)$data['name'];
    if (array_key_exists('link', $data))            $this->link          = (string)$data['link'];
    if (array_key_exists('token', $data))           $this->token         = (string)$data['token'];
    if (array_key_exists('article', $data))         $this->article       = (string)$data['article'];
    if (array_key_exists('bar_code', $data))        $this->barcode       = (string)$data['bar_code'];
    if (array_key_exists('announce', $data))        $this->announce      = (string)$data['announce'];
    if (array_key_exists('description', $data))     $this->description   = (string)$data['description'];
    if (array_key_exists('media_image', $data))     $this->mediaImage    = (string)$data['media_image'];
    if (array_key_exists('price', $data))           $this->price         = (float)$data['price'];
  }

  /**
   * @param string $announce
   */
  public function setAnnounce($announce)
  {
    $this->announce = $announce;
  }

  /**
   * @return string
   */
  public function getAnnounce()
  {
    return $this->announce;
  }

  /**
   * @param string $article
   */
  public function setArticle($article)
  {
    $this->article = $article;
  }

  /**
   * @return string
   */
  public function getArticle()
  {
    return $this->article;
  }

  /**
   * @param string $barCode
   */
  public function setBarcode($barCode)
  {
    $this->barcode = $barCode;
  }

  /**
   * @return string
   */
  public function getBarcode()
  {
    return $this->barcode;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
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
   * @param string $mediaImage
   */
  public function setMediaImage($mediaImage)
  {
    $this->mediaImage = $mediaImage;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
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
   * @param float $price
   */
  public function setPrice($price)
  {
    $this->price = $price;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
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
}
