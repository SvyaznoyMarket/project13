<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 01.06.12
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */
require_once('ProductKitData.php');
require_once(__DIR__.'/../CategoryShortData.php');

class ProductData
{
  /** @var int */
  private $id;

  /** @var int */
  private $viewId;

  /** @var int */
  private $setId;

  /** @var bool */
  private $isModel;

  /** @var bool */
  private $isPrimaryLine;

  /** @var int */
  private $modelId;

  /** @var int */
  private $typeId;

  /** @var int */
  private $score;

  /** @var string */
  private $name;

  /** @var string */
  private $link;

  /** @var string */
  private $token;

  /** @var string */
  private $nameWeb;

  /** @var string */
  private $prefix;

  /** @var string */
  private $article;

  /** @var string */
  private $barcode;

  /** @var string */
  private $tagline;

  /** @var string */
  private $announce;

  /** @var string */
  private $description;

  /** @var string */
  private $mediaImage;

  /** @var int */
  private $rating;

  /** @var int */
  private $ratingCount;

  /** @var int */
  private $commentCount;

  /** @var int */
  private $commentsNum;

  /** @var float */
  private $price;

  /** @var float */
  private $priceAverage;

  /** @var ProductKitData[]  */
  private $kitList;

  /** @var CategoryShortData[]  */
  private $categoryList;

  public function __construct(array $data = array())
  {
    if (array_key_exists('id', $data))              $this->id            = (int)$data['id'];
    if (array_key_exists('view_id', $data))         $this->viewId        = (int)$data['view_id'];
    if (array_key_exists('set_id', $data))          $this->setId         = (int)$data['set_id'];
    if (array_key_exists('is_model', $data))        $this->isModel       = (bool)$data['is_model'];
    if (array_key_exists('is_primary_line', $data)) $this->isPrimaryLine = (bool)$data['is_primary_line'];
    if (array_key_exists('model_id', $data))        $this->modelId       = (int)$data['model_id'];
    if (array_key_exists('type_id', $data))         $this->typeId        = (int)$data['type_id'];
    if (array_key_exists('score', $data))           $this->score         = (int)$data['score'];
    if (array_key_exists('name', $data))            $this->name          = (string)$data['name'];
    if (array_key_exists('link', $data))            $this->link          = (string)$data['link'];
    if (array_key_exists('token', $data))           $this->token         = (string)$data['token'];
    if (array_key_exists('name_web', $data))        $this->nameWeb       = (string)$data['name_web'];
    if (array_key_exists('prefix', $data))          $this->prefix        = (string)$data['prefix'];
    if (array_key_exists('article', $data))         $this->article       = (string)$data['article'];
    if (array_key_exists('bar_code', $data))        $this->barcode       = (string)$data['bar_code'];
    if (array_key_exists('tagline', $data))         $this->tagline       = (string)$data['tagline'];
    if (array_key_exists('announce', $data))        $this->announce      = (string)$data['announce'];
    if (array_key_exists('description', $data))     $this->description   = (string)$data['description'];
    if (array_key_exists('media_image', $data))     $this->mediaImage    = (string)$data['media_image'];
    if (array_key_exists('rating', $data))          $this->rating        = (int)$data['rating'];
    if (array_key_exists('rating_count', $data))    $this->ratingCount   = (int)$data['rating_count'];
    if (array_key_exists('comments_num', $data))    $this->commentsNum   = (int)$data['comments_num'];
    if (array_key_exists('comment_count', $data))   $this->commentCount  = (int)$data['comment_count'];
    if (array_key_exists('price', $data))           $this->price         = $data['price'];
    if (array_key_exists('price_average', $data))   $this->priceAverage  = $data['price_average'];

    if (array_key_exists('kit', $data)){
      foreach($data['kit'] as $kit){
        $this->addKit(new ProductKitData($kit));
      }
    }

    if (array_key_exists('category', $data)){
      foreach($data['category'] as $category){
        $this->addCategory(new CategoryShortData($category));
      }
    }
  }

  /**
   * @param ProductKitData $kit
   */
  public function addKit(ProductKitData $kit)
  {
    $kit->setRelatedProductId($this->id);
    $this->kitList[] = $kit;
  }

  /**
   * @param CategoryShortData $category
   */
  public function addCategory(CategoryShortData $category)
  {
    $this->categoryList[] = $category;
  }

  /**
   * @return bool
   */
  public function isKit(){
    return ($this->getSetId() ==2);
  }

  /**
   * @return string
   */
  public function getAnnounce()
  {
    return $this->announce;
  }

  /**
   * @return string
   */
  public function getArticle()
  {
    return $this->article;
  }

  /**
   * @return string
   */
  public function getBarcode()
  {
    return $this->barcode;
  }

  /**
   * @return int
   */
  public function getCommentCount()
  {
    return $this->commentCount;
  }

  /**
   * @return int
   */
  public function getCommentsNum()
  {
    return $this->commentsNum;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return boolean
   */
  public function isModel()
  {
    return $this->isModel;
  }

  /**
   * @return boolean
   */
  public function isPrimaryLine()
  {
    return $this->isPrimaryLine;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
  }

  /**
   * @return int
   */
  public function getModelId()
  {
    return $this->modelId;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getNameWeb()
  {
    return $this->nameWeb;
  }

  /**
   * @return string
   */
  public function getPrefix()
  {
    return $this->prefix;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @return float
   */
  public function getPriceAverage()
  {
    return $this->priceAverage;
  }

  /**
   * @return int
   */
  public function getRating()
  {
    return $this->rating;
  }

  /**
   * @return int
   */
  public function getRatingCount()
  {
    return $this->ratingCount;
  }

  /**
   * @return int
   */
  public function getScore()
  {
    return $this->score;
  }

  /**
   * @return int
   */
  public function getSetId()
  {
    return $this->setId;
  }

  /**
   * @return string
   */
  public function getTagline()
  {
    return $this->tagline;
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * @return int
   */
  public function getViewId()
  {
    return $this->viewId;
  }

  /**
   * @return ProductKitData[]
   */
  public function getKitList()
  {
    return $this->kitList;
  }

  /**
   * @return int
   */
  public function getTypeId()
  {
    return $this->typeId;
  }

  /**
   * @return \light\CategoryShortData
   */
  public function getMainCategory() {
    return reset($this->categoryList);
  }
}
