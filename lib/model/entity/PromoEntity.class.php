<?php

/**
 * Промо
 */
class PromoEntity
{
  const TYPE_STANDART = 1;
  const TYPE_DUMMY = 2;
  const TYPE_EXCLUSIVE = 3;

  /* @var integer */
  private $id;

  /** @var integer */
  private $type;

  /* @var string */
  private $name;

  /* @var string */
  private $image;

  /** @var string */
  private $url;

  /** @var string */
  private $countUrl;

  /** @var array */
  private $items = array();


  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data)) $this->id = (int)$data['id'];
    if (array_key_exists('type_id', $data)) $this->type = (int)$data['type_id'];
    if (array_key_exists('name', $data)) $this->name = $data['name'];
    if (array_key_exists('media_image', $data)) $this->image = $data['media_image'];
    if (array_key_exists('url', $data)) $this->url = $data['url'];
    if (array_key_exists('show_count_url', $data)) $this->countUrl = $data['show_count_url'];

    if (array_key_exists('item_list', $data)) foreach ($data['item_list'] as $i) {
      $this->items[] = new PromoItemEntity($i);
    }
  }

  /**
   * @param string $countUrl
   */
  public function setCountUrl($countUrl)
  {
    $this->countUrl = $countUrl;
  }

  /**
   * @return string
   */
  public function getCountUrl()
  {
    return $this->countUrl;
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
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }

  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * @param array $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }

  /**
   * @return array
   */
  public function getItems()
  {
    return $this->items;
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

  public function getImageUrl($view = 0)
  {
    $urls = sfConfig::get('app_banner_image_url');

    return $this->image ? $urls[$view].$this->image : null;
  }

  public function isExclusive()
  {
    return 'exclusive' == $this->type;
  }

  public function isDummy()
  {
    return 'dummy' == $this->type;
  }

  public function isBanner()
  {
    return 'banner' == $this->type;
  }
}
