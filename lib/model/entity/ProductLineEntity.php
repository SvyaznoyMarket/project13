<?php

class ProductLineEntity
{
  /** @var int */
  private $id;
  /** @var string */
  private $token;
  /** @var string */
  private $name;
  /** @var string */
  private $description;
  /** @var string */
  private $mediaImage;
  /** @var int */
  private $productCount;
  private $kitCount;
  /** @var int */
  private $mainProductId;
  /** @var ProductEntity */
  private $mainProduct;
  /** @var int[] */
  private $productIdList = array();
  private $kitIdList = array();
  /** @var ProductEntity[] */
  private $productList = array();
  private $kitList = array();

  /**
   * @param array $data
   */
  public function __construct(array $data = array())
  {
    if (isset($data['id']))                 $this->setId($data['id']);
    if (isset($data['token']))              $this->setToken($data['token']);
    if (isset($data['name']))               $this->setName($data['name']);
    if (isset($data['description']))        $this->setDescription($data['description']);
    if (isset($data['media_image']))        $this->setMediaImage($data['media_image']);
    if (isset($data['main_product_id']))    $this->setMainProductId($data['main_product_id']);
    if (isset($data['product_id_list']))    $this->setProductIdList((array)$data['product_id_list']);
    if (isset($data['kit_id_list']))        $this->setKitIdList((array)$data['kit_id_list']);
    if (isset($data['product_count']))      $this->setProductCount($data['product_count']);
    if (isset($data['kitCount']))           $this->setKitCount($data['kit_count']);
    if (is_null($this->productCount)){
      $this->productCount = count($this->productIdList);
    }
    if (is_null($this->kitCount)){
      $this->productCount = count($this->kitIdList);
    }
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
   * @param $media_image
   */
  public function setMediaImage($media_image)
  {
    $this->mediaImage = (string)$media_image;
  }

  /**
   * @return string
   */
  public function getMediaImage()
  {
    return $this->mediaImage;
  }

  /**
   * @param int $viewId
   * @return null|string
   */
  public function getMediaImageUrl($viewId = 1)
  {
    if ($this->mediaImage) {
      $urls = sfConfig::get('app_product_photo_url');
      return $urls[$viewId] . $this->mediaImage;
    }
    else {
      return null;
    }
  }

  /**
   * @param $name
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

  /**
   * @return int
   */
  public function getProductCount()
  {
    return $this->productCount;
  }

  /**
   * @param int $productCount
   */
  public function setProductCount($productCount)
  {
    $this->productCount = (int)$productCount;
  }

  /**
   * @return string
   */
  public function getLink()
  {
    return sfContext::getInstance()->getRouting()->generate('lineCard', array('line' => $this->getToken()));
  }

  public function getNavigation()
  {
    $list = array();

    if($this->getMainProduct())
    foreach($this->getMainProduct()->getCategoryList() as $category)
    {
      $list[] = array(
        'name' => $category->getName(),
        'url'  => $category->getLink(),
      );
    }

    $list[] = array(
      'name' => 'Серия '.$this->getName(),
      'url'  => $this->getLink(),
    );
    return $list;
  }

  /**
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = (string)$description;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = (string)$token;
  }

  /**
   * @return string|null
   * @todo move to Soa data loading
   */
  public function getToken()
  {
    /** @var $table TaskTable */
    $table = ProductLineTable::getInstance();
    /** @var $line ProductLine */
    $line = $table->getByCoreId($this->id);
    if($line){
      $token = $line->token;
      $this->token = $token;
    }
    return $this->token;
  }

  /**
   * @param int $mainProductId
   */
  public function setMainProductId($mainProductId)
  {
    $this->mainProductId = (int)$mainProductId;
  }

  /**
   * @return int
   */
  public function getMainProductId()
  {
    return $this->mainProductId;
  }

  /**
   * @param int[] $productListId
   */
  public function setProductIdList(array $productListId)
  {
    $this->productIdList = array();
    foreach($productListId as $id)
      $this->productIdList[] = (int)$id;
    $this->productCount = count($this->productIdList);
  }

  /**
   * @return int[]
   */
  public function getProductIdList()
  {
    return $this->productIdList;
  }

  /**
   * @param int[] $kitListId
   */
  public function setKitIdList(array $kitListId)
  {
    $this->kitIdList = array();
    foreach($kitListId as $id)
      $this->kitIdList[] = (int)$id;
  }

  /**
   * @return int[]
   */
  public function getKitIdList()
  {
    return $this->kitIdList;
  }

  /**
   * @param ProductEntity $mainProduct
   */
  public function setMainProduct(ProductEntity $mainProduct)
  {
    $this->mainProduct = $mainProduct;
  }

  /**
   * @return ProductEntity
   */
  public function getMainProduct()
  {
    return $this->mainProduct;
  }

  public function getFullProductIdList()
  {
    return array_merge($this->kitIdList, $this->productIdList);
  }

  /**
   * @return ProductEntity[]
   */
  public function getFullProductList()
  {
    return array_merge($this->kitList, $this->productList);
  }

  /**
   * @param ProductEntity[] $productList
   */
  public function setFullProductList(array $productList)
  {
    $this->productList = array();
    $this->kitList = array();
    foreach($productList as $product){
      if(in_array($product->getId(), $this->productIdList)){
        $this->productList[] = $product;
      }
      if(in_array($product->getId(), $this->kitIdList)){
        $this->kitList[] = $product;
      }
    }
  }

  /**
   * @param ProductEntity[] $productList
   */
  public function setProductList(array $productList)
  {
    foreach($productList as $product)
    {
      assert($product instanceof ProductEntity);
      $this->productList = $productList;
    }
  }

  /**
   * @return ProductEntity[]
   */
  public function getProductList()
  {
    return $this->productList;
  }

  /**
   * @param ProductEntity[] $kitList
   */
  public function setKitList(array $kitList)
  {
    foreach($kitList as $kit)
    {
      assert($kit instanceof ProductEntity);
      $this->kitList = $kitList;
    }
  }

  /**
   * @return ProductEntity[]
   */
  public function getKitList()
  {
    return $this->kitList;
  }

  /**
   * @param int $kitCount
   */
  public function setKitCount($kitCount)
  {
    $this->kitCount = (int)$kitCount;
  }

  /**
   * @return int
   */
  public function getKitCount()
  {
    return $this->kitCount;
  }

  /**
   * @return int
   */
  public function getTotalCount()
  {
    return $this->productCount + $this->kitCount;
  }
}
