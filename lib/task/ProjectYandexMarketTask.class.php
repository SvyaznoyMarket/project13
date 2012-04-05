<?php

class ProjectYandexMarketTask extends sfBaseTask
{

  private $_companyData = array(
    'name' => 'Enter.ru',
    'company' => 'Enter.ru',
    'url' => 'http://www.enter.ru',
    'email' => 'enter@enter.ru'
  );


  private $_xmlFolder = 'web/xml';

  /**
   * Список категорий, данные из которых выгружаем
   * @var array
   * для каждого элемента:
   *  - min_price - минимальная цена, с которой можно выгружать
   *  - product_num - максимальное количество товаров, выгружаемое для этой категории
   *  Пример:
  '2' => array(
  'min_price' => 200,
  'max_num' => 5,
  ),
  '8' => array(
  'min_price' => 300,
  'max_num' => 10,
  )
   */
  private $_categoryList = array();

//DEPRICATED
//    /**
//     * Флаг - выгжужать  url категорий
//     * @var boolean
//     */
//    private $_uploadCategotyUrl = true;
//    /**
//     * Используется только, если $_uploadCategotyUrl = true;
//     * Список файлов, для которых выгружать URL
//     * Если не задано - выгружать для всех
//     * @var type
//     */
//    private $_uploadCategotyUrlFileList = array(
//        'export_mgcom.xml',
//        'export_realweb.xml',
//        'export_mgcom_ryazan.xml',
//        'export_realweb_ryazan.xml',
//        'export_mgcom_lipetsk.xml',
//        'export_realweb_lipetsk.xml',
//        'export_mgcom_belgorod.xml',
//        'export_realweb_belgorod.xml',
//        'export_mgcom_orel.xml',
//        'export_realweb_orel.xml',
//    );

  /**
   * Список товаров, выгружаемых для товара
   * @var array
   */
  private $_uploadParamsList = array(
    'url',
    'price',
    'categoryId',
    'picture',
    'typePrefix',
    'vendor',
    'model',
    'name',
    'pickup',
    'description',
    'local_delivery_cost',
    'delivery',
  );

  /**
   * Дополнительные параметры в выгрузке продуктов
   * @var array
   * для каждого элемента:
   *   - name - имя для выгрузки
   *   - type = core/fix - из ядра, либо фиксированное значение
   *   - field - название_параметра_в_core (только для type==core)
   *   - value - значение (только для type==fix)
   *   - file - выгружать ТОЛЬКО для этого файла (не обязательное)
   *  Пример:
  array(
  'name' => 'test',
  'type' => 'fix',
  'value' => 88
  'file' => 'export_mgcom.xml'
  ),
  array(
  'name' => 'article-value',
  'type' => 'core',
  'field' => 'article'
  'file' => 'export_mgcom.xml'
  )
   * */
  private $_additionalParams = array(
    array(
      'name' => 'currencyId',
      'type' => 'fix',
      'value' => 'RUR',
      'file' => 'export_mgcom.xml'
    )
  );

  /**
   * Результат целиком
   *
   * @var simpleXmlElement
   */
  private $_xmlResult;

  /**
   * Узел shop
   *
   * @var simpleXmlElement
   */
  private $_xmlResultShop;

  /**
   * По сколько штук записывать в файл категории
   * @var integer
   */
  private $_portionToLoadCategory = 200;

  /**
   * По сколько штук записывать в файл товары
   * @var integer
   */
  private $_portionToLoadProduct = 5000;


  /**
   * Текущий регион
   * @var Region
   */
  private $_currentRegion;


  /**
   * id региона по умолчанию
   * @var int
   */
  private $_defaultRegionId = 19355;

  /**
   * Рутовые категории, из которых выгружаем в разные файлы
   * @var type
   */
  private $_globalCatList = array(
    //для всех
    array(
      'name' => 'ya_market.xml',
      'region_id' => 19355,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_realweb.xml',
      'list' => array(6, 8, 9),
      'region_id' => 19355,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_mgcom.xml',
      'list' => array(3, 2, 1, 4, 7, 8, 5),
      'region_id' => 19355,
      'min_num' => 3,
    ),
    array(
      'name' => 'max2.xml',
      'region_id' => 19355,
      'max_num' => 2
    ),
    //для Рязани
    array(
      'name' => 'ya_market_ryazan.xml',
      'region_id' => 10375,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_realweb_ryazan.xml',
      'list' => array(6, 8, 9),
      'region_id' => 10375,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_mgcom_ryazan.xml',
      'list' => array(3, 2, 1, 4, 7, 8, 5),
      'region_id' => 10375,
      'min_num' => 3,
    ),
    array(
      'name' => 'max2_ryazan.xml',
      'region_id' => 10375,
      'max_num' => 2
    ),
    //для Липецка
    array(
      'name' => 'ya_market_lipetsk.xml',
      'region_id' => 100,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_realweb_lipetsk.xml',
      'list' => array(6, 8, 9),
      'region_id' => 100,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_mgcom_lipetsk.xml',
      'list' => array(3, 2, 1, 4, 7, 8, 5),
      'region_id' => 100,
      'min_num' => 3,
    ),
    array(
      'name' => 'max2_lipetsk.xml',
      'region_id' => 100,
      'max_num' => 2
    ),
    //для Белгорода
    array(
      'name' => 'ya_market_belgorod.xml',
      'region_id' => 13242,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_realweb_belgorod.xml',
      'list' => array(6, 8, 9),
      'region_id' => 13242,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_mgcom_belgorod.xml',
      'list' => array(3, 2, 1, 4, 7, 8, 5),
      'region_id' => 13242,
      'min_num' => 3,
    ),
    array(
      'name' => 'max2_belgorod.xml',
      'region_id' => 13242,
      'max_num' => 2
    ),
    //для Орла
    array(
      'name' => 'ya_market_orel.xml',
      'region_id' => 13243,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_realweb_orel.xml',
      'list' => array(6, 8, 9),
      'region_id' => 13243,
      'min_num' => 3,
    ),
    array(
      'name' => 'export_mgcom_orel.xml',
      'list' => array(3, 2, 1, 4, 7, 8, 5),
      'region_id' => 13243,
      'min_num' => 3,
    ),
    array(
      'name' => 'max2_orel.xml',
      'region_id' => 13243,
      'max_num' => 2
    ),
  );

  /**
   * Id бизнес-юнита ювелирки.
   * Для него особые правила для доставки.
   *
   * @var integer
   */
  private $_jewelUnit = 9;


  /**
   * Информация о файле, который сейчас обрабатывается
   * @var array
   */
  private $_currentFileInfo;


  private $_categoryLimitExist;

  private $_rsMYSQL;

  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace = 'Project';
    $this->name = 'YandexMarket';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [Project:YandexMarket|INFO] выгружает ряд сущностей в формате XML для экспорта в Yandex Market.
Call it with:

  [php symfony Project:YandexMarket|INFO]
EOF;

  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    //   $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    //$this->conn = Doctrine_Manager::connection();

    if (!file_exists($this->_xmlFolder)) mkdir($this->_xmlFolder);

    $config = sfConfig::getAll();
    $this->_imageUrlsConfig = $config['app_product_photo_url'];
    #var_dump($this->_imageUrlsConfig);
    #exit();

    $this->_generateCatList();

    //получаем кофиг Mysql
    $configMysql = sfDatabaseConfigHandler::getConfiguration(array('config/databases.yml'));
    $configMysql = $configMysql['doctrine']['param'];
    //var_dump($configMysql);
    if (!isset($configMysql['host'])) {
      echo 'Пожалуйста, укажите host для соединения mysql.';
      return;
    }
    $this->_rsMYSQL = mysql_connect(trim($configMysql['host']), trim($configMysql['username']), trim($configMysql['password']));

    mysql_query('SET CHARSET "UTF8"', $this->_rsMYSQL);
    mysql_query('use enter', $this->_rsMYSQL);

    //генерируем файлы с определёнными категориями

    if (count($this->_globalCatList) > 0)
      foreach ($this->_globalCatList as $partInfo) {
        $this->_currentFileInfo = $partInfo;
        $this->_currentRegion = RegionTable::getInstance()->getQueryObject()->addWhere('id = ?', $partInfo['region_id'])->fetchArray();
        if (isset($this->_currentRegion[0])) {
          $this->_currentRegion = $this->_currentRegion[0];
        }
        //print_r($this->_currentRegion);
        //die();
        if (!$this->_currentRegion || !isset($this->_currentRegion['id'])) {
          continue;
        }
        //заполняем массив категорий
        $this->_categoryList = array();
        if ($partInfo['inner']) {
          foreach ($partInfo['inner'] as $catId) {
            $this->_categoryList[$catId] = array();
          }
        }
        $this->_xmlFilePathReal = $this->_xmlFolder . '/' . $partInfo['name'];
        $this->_xmlFilePath = str_replace('.xml', '_tmp.xml', $this->_xmlFilePathReal);

        //выполняем саму генарацию
        $this->_xmlGenerateItself();

        copy($this->_xmlFilePath, $this->_xmlFilePathReal);
        unlink($this->_xmlFilePath);

      }
    #print_r($this->_categoryList);
    #exit();

  }

  private function _xmlGenerateItself()
  {
    //корневой каталог
    $this->_xmlResult = new SimpleXMLElement("<yml_catalog date='" . date("Y-m-d H:i") . "'></yml_catalog>");
    file_put_contents($this->_xmlFilePath, '<?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd">');
    file_put_contents($this->_xmlFilePath, "<yml_catalog  date='" . date("Y-m-d H:i") . "'>", FILE_APPEND);
    //базовое
    $this->_setShop();
    file_put_contents($this->_xmlFilePath, '</yml_catalog>', FILE_APPEND);
  }

  public function _generateCatList()
  {
    //глобальные списки глобальных категорий
    foreach ($this->_globalCatList as $k => $catList) {
      if (isset($catList['list'])) {
        $idList = array();
        $catListData = Doctrine_Core::getTable('ProductCategory')
          ->createQuery('pc')
          ->where('pc.root_id IN (' . implode(',', $catList['list']) . ')')
          ->fetchArray();
        ;
        foreach ($catListData as $cat) $idList[] = $cat['id'];
        $this->_globalCatList[$k]['inner'] = $idList;
      } else {
        $this->_globalCatList[$k]['inner'] = false;
      }

    }
    #  exit();
  }


  /**
   * Дабавляет базовую информацию о магазине
   */
  private function _setShop()
  {
    file_put_contents($this->_xmlFilePath, '<shop>', FILE_APPEND);

    $this->_xmlResultShop = $this->_xmlResult->addChild('shop');
    $next = $this->_xmlResultShop->addChild('name', $this->_companyData['name']);
    file_put_contents($this->_xmlFilePath, $next->asXML(), FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('company', $this->_companyData['company']);
    file_put_contents($this->_xmlFilePath, $next->asXML(), FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('url', $this->_companyData['url']);
    file_put_contents($this->_xmlFilePath, $next->asXML(), FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('email', $this->_companyData['email']);
    file_put_contents($this->_xmlFilePath, $next->asXML(), FILE_APPEND);

    //file_put_contents($this->_xmlFilePath,$this->_xmlResultShop->asXML(),FILE_APPEND);

    //валюты
    $this->_setCurrencyList();
    //категории товаров
    $this->_setCategoryList();
    //товары
    $this->_setOffersList();

    file_put_contents($this->_xmlFilePath, '</shop>', FILE_APPEND);

  }

  /**
   * Добавляет в xml список валют (единственную валюту - рубль)
   */
  private function _setCurrencyList()
  {
    $currencies = $this->_xmlResultShop->addChild('currencies');
    //валюта - единственная
    $currency = $currencies->addChild('currency');
    $currency->addAttribute('id', 'RUR');
    $currency->addAttribute('rate', '1');
    file_put_contents($this->_xmlFilePath, $currencies->asXML(), FILE_APPEND);
  }

  /**
   * Добавляет в xml список категорий товаров
   */
  private function _setCategoryList()
  {
    //ваясним, выгружать ли URL категорий в текущий файл
    $addCategoryUrl = true;
    //DEPRICATED
    //        if ($this->_uploadCategotyUrl) {
    //            if (isset($this->_uploadCategotyUrlFileList)) {
    //                $addCategoryUrl = false;
    //                foreach($this->_uploadCategotyUrlFileList as $trueFile) {
    //                    if (strpos($this->_xmlFilePathReal, $trueFile) !== false ) {
    //                        $addCategoryUrl = true;
    //                        break;
    //                    }
    //                }
    //            } else {
    //                $addCategoryUrl = true;
    //            }
    //        } else {
    //            $addCategoryUrl = true;
    //        }

    $categoryList = ProductCategoryTable::getInstance()->createBaseQuery();
    if (count($this->_categoryList)) {
      $categoryList = $categoryList->whereIn('id', array_keys($this->_categoryList));
    }
    $categoryList = $categoryList
    #->limit(50)
      ->fetchArray();
    foreach ($categoryList as $cat) {
      $catIdToCoreId[$cat['core_id']] = $cat['id'];
    }
    $cats = $this->_xmlResultShop->addChild('categories');

    file_put_contents($this->_xmlFilePath, '<categories>', FILE_APPEND);
    $numInRound = 0;
    $currentXml = "";
    foreach ($categoryList as $categoryInfo) {
      $cat = $cats->addChild('category', $categoryInfo['name']);
      $cat->addAttribute('id', $categoryInfo['core_id']);
      if ($categoryInfo['core_parent_id'] && isset($catIdToCoreId[$categoryInfo['core_parent_id']])) $cat->addAttribute('parentId', $catIdToCoreId[$categoryInfo['core_parent_id']]);
      //если нужно добавить url
      if ($addCategoryUrl) {
        $cat->addAttribute('url', $this->_companyData['url'] . $this->getRouting()->generate('productCatalog_category', array('productCategory' => (!empty($categoryInfo['token_prefix']) ? ($categoryInfo['token_prefix'] . '/' . $categoryInfo['token']) : $categoryInfo['token']))));
        //$cat->addAttribute('url',$this->_companyData['url'].'/catalog/'.$categoryInfo['token'].'/');
      }

      $numInRound++;
      //записываем в файл порциями по 200 штук
      //чтобы не открывать файл слишком много раз
      if ($numInRound >= $this->_portionToLoadCategory) {
        $numInRound = 0;
        file_put_contents($this->_xmlFilePath, $currentXml, FILE_APPEND);
        $currentXml = "";
      }
      $currentXml .= $cat->asXML();
    }
    file_put_contents($this->_xmlFilePath, $currentXml . '</categories>', FILE_APPEND);

  }

  /**
   * Добавляет в XML список товаров
   */
  private function _setOffersList()
  {


    //узел продуктов
    $offers = $this->_xmlResultShop->addChild('offers');

    $this->_categoryLimitExist = false;
    if (count($this->_categoryList) > 0) $this->_categoryLimitExist = true;
    //учитываем список категорий, если он задан, для будующей выборки
    $catIdListString = array();
    if ($this->_categoryLimitExist) {
      foreach ($this->_categoryList as $catId => $catInfo) {
        $catIdList[] = $catId;
      }
      $catIdListString = implode(',', $catIdList);
    }


    $currentOffset = 0;
    //        $this->_portionToLoadProduct = 5;
    //        $tmpNum = 1;
    $currentXml = '';
    file_put_contents($this->_xmlFilePath, '<offers>', FILE_APPEND);
    do {
      //            if ($tmpNum>=2) {
      //                break;
      //            }
      //            $tmpNum++;

      //делаем выборку товаров
      $sql = $this->_makeProductListQuery($catIdListString);
      $sql .=
        ' LIMIT ' . $this->_portionToLoadProduct . '
                 OFFSET ' . $currentOffset;
      $currentOffset += $this->_portionToLoadProduct;

      $listRs = mysql_query($sql, $this->_rsMYSQL) or die(mysql_error());
      $offersList = array();
      while ($row = mysql_fetch_assoc($listRs)) {
        $offersList[] = $row;
      }
      //                       echo 'count---'.count($offersList) . $this->_xmlFilePath . "=====\n";
      //                        die();

      // для каждого выбранного продукта
      foreach ($offersList as $offerInfo) {
        $this->_currentIsAvalible = true;

        try {
          //генерируем узел для XML. (внутри проверка - возможно этот продукт вообще не выгружаем)
          $offer = $this->_generateOneOfferNode($offerInfo);
          //если продукт всётаки, не выгружаем
          if (!$offer) { // || !$this->_currentDeliveyIsAvalible) {
            continue;
          }
        } catch (Exception $e) {
          #echo 'eeroor--'.$e->getMessage().$e->getFile().'=='.$e->getLine().'        ';
          continue;
        }

        $currentXml .= $offer; //->asXML();
        unset($offer);

      }
      file_put_contents($this->_xmlFilePath, $currentXml, FILE_APPEND);
      $currentXml = "";

    } while (count($offersList));
    file_put_contents($this->_xmlFilePath, '</offers>', FILE_APPEND);


  }


  private function _generateOneOfferNode($offerInfo)
  {

    //если ограничения по категориям существуют - проверяем каждый товар
    //возможно его цена меньше разрешенной
    //либо достаточное для данной категории количество уже набрано
    //это не было учтено при выборке!
    if ($this->_categoryLimitExist) {
      $productCatId = $offerInfo['product_category_id'];
      //если не проходим ограничения по цене
      if (isset($this->_categoryList[$productCatId]['min_price']) && $offerInfo['price'] < $this->_categoryList[$productCatId]['min_price']) {
        return null;
      }
      //проверяем максимальное количество товаров для этой категории
      if (isset($this->_categoryList[$productCatId]['max_num'])) {
        //если нужное количество уже набрали
        if (isset($resultAddedList[$productCatId]) && count($resultAddedList[$productCatId]) >= $this->_categoryList[$productCatId]['max_num']) {
          return null;
        }
        //считаем, сколько уже набранно
        $resultAddedList[$productCatId][] = $offerInfo['id'];
      }
    }


    $offer = $this->_xmlResultShop->addChild('offer');
    //создаём узел продукта и устанавливаем значения атрибутов
    if ($offerInfo['is_instock']) {
      $inStock = 'true';
    }
    else {
      $inStock = 'false';
    }
    $offer->addAttribute('id', $offerInfo['core_id']);
    $offer->addAttribute('type', 'vendor.model');
    $offerInner = '';

    //основные параметры
    foreach ($this->_uploadParamsList as $param) {
      $value = $this->_getPropValueByCode($offerInfo, $param);
      if ($param && $value !== false) {
        $value = htmlspecialchars($value);
        $offer->addChild($param, $value);
      }
    }
    if ($this->_currentIsAvalible === false) {
      $inStock = 'false';
    }
    $offer->addAttribute('available', $inStock);

    //дополнительные параметры
    foreach ($this->_additionalParams as $addParam) {
      $value = $this->_getAdditionalPropValueByCode($offerInfo, $addParam);
      if ($value) {
        $offer->addChild($addParam['name'], $value);
      }
    }
    //echo $offer;
    //die();
    $xml = $offer->asXML();
    unset($offer);
    return $xml;

  }


  private function _makeProductListQuery($catIdListString = array())
  {

    $stockCondition = '';
    if (isset($this->_currentFileInfo['min_num'])) {
      //берем только продукты, доступные на МОЛКОМЕ в количестве не менее 3 шт
      $stockCondition =
        ' AND  (ps.is_supplied="1" OR spr.quantity>"' . $this->_currentFileInfo['min_num'] . '")
            ';
    } elseif (isset($this->_currentFileInfo['max_num'])) {
      //берем только продукты, с правильной ссылкой
      $stockCondition =
        ' AND (ps.is_store="1" AND spr.quantity<="' . $this->_currentFileInfo['max_num'] . '")
            ';
    } else {
      $stockCondition =
        ' AND (ps.is_supplied="1" OR ps.is_store="1")
            ';
    }

    $sql = '
            SELECT
            p.id, p.core_id, p.name, p.description, p.token_prefix, p.token, p.prefix, p.main_photo,
            pcpr.product_category_id, pcat.core_id as product_category_core_id, pcat.root_id as category_root_id, creator.name as creator_name,
            pp.price, pdp.price as delivery_price, ps.is_instock
            FROM `product` as p
            LEFT JOIN `stock_product_relation` as spr on spr.product_id=p.id
            LEFT JOIN `product_state` as ps on p.id=ps.product_id AND ps.region_id="' . $this->_currentRegion['id'] . '"  ' . $stockCondition . '
            LEFT JOIN `creator` on p.creator_id=creator.id
            LEFT JOIN `product_delivery_price` as pdp on pdp.product_id=p.id
            LEFT JOIN `product_price` as pp on pp.product_id=p.id
            LEFT JOIN `product_category_product_relation` as pcpr on pcpr.product_id=p.id
            LEFT JOIN `product_category` as pcat on pcat.id=pcpr.product_category_id

            WHERE
            ps.view_list = "1" AND
            pdp.price_list_id = "' . $this->_currentRegion['product_price_list_id'] . '" AND
            ( p.model_id IS NULL OR p.is_model = "1" ) AND
            spr.stock_id="' . $this->_currentRegion['stock_id'] . '"  AND
            pp.product_price_list_id = "' . $this->_currentRegion['product_price_list_id'] . '" AND
            p.token_prefix IS NOT NULL AND
            pdp.price_list_id = "' . $this->_currentRegion['product_price_list_id'] . '"
            ';

    //если есть ограничения по категориям
    if (isset($catIdListString) && count($catIdListString)) {
      $sql .= ' AND pcpr.product_category_id IN (' . $catIdListString . ') ';
    }
    $sql .= ' GROUP BY p.id';
    //echo $sql;

    return $sql;

  }


  private function _getPropValueByCode($offerInfo, $code)
  {
    $value = false;
    switch ($code) {
      case 'url':
        $value = $this->_companyData['url'] . $this->getRouting()->generate('productCard', array('product' => $offerInfo['token_prefix'] . '/' . $offerInfo['token']));
        if ($this->_currentRegion['id'] != $this->_defaultRegionId) {
          $value .= '?city_id=' . $this->_currentRegion['token'];
        }
        break;
      case 'price':
        if (isset($offerInfo['price'])) {
          $value = $offerInfo['price'];
        } else {
          $value = '0.00';
          $this->_currentIsAvalible = false;
        }
        break;
      case 'categoryId':
        if (isset($offerInfo['product_category_core_id']))
          $value = $offerInfo['product_category_core_id'];
        break;
      case 'picture':
        if (isset($offerInfo['main_photo']) && $offerInfo['main_photo']) {
          $value = $this->_imageUrlsConfig[3] . $offerInfo['main_photo'];
        } else {
          $value = '';
          $this->_currentIsAvalible = false;
        }
        break;
      case 'typePrefix':
        #$value = $offerInfo['Type']['name'];
        $value = $offerInfo['prefix'];
        break;
      case 'vendor':
        $value = $offerInfo['creator_name'];
        break;
      case 'model':
        $value = trim(str_replace(array($offerInfo['prefix'], $offerInfo['creator_name']), '', $offerInfo['name']));
        break;
      case 'name':
        $value = ''; //$prodObject->getName();
        break;
      case 'pickup':
        $value = 'true';
        break;
      case 'delivery':
        //для ювелирки доставки никогда нет
        if (isset($offerInfo['category_root_id'])
          && $offerInfo['category_root_id'] == $this->_jewelUnit
        ) {
          $value = 'false';
          $this->_currentDeliveyIsAvalible = 1;
          //для остальных - отображаем только те, у которых есть доставка
        } else {
          $value = 'true';
        }
        break;
      case 'description':
        $value = $offerInfo['description'];
        break;
      case 'local_delivery_cost':
        if (isset($offerInfo['delivery_price'])) {
          $value = $offerInfo['delivery_price'];
          $this->_currentDeliveyIsAvalible = 1;
        } else {
          $value = false;
          $this->_currentDeliveyIsAvalible = 0;
        }
        break;
    }
    return $value;
  }


  private function _getAdditionalPropValueByCode($productInfo, $param)
  {
    $value = '';
    //если надо задавать только для определённого файла
    if (isset($param['file'])) {
      //если сейчас не этот файл, ничего не делаем
      if (strpos($this->_xmlFilePath, $param['file']) == false) {
        return false;
      }
    }
    switch ($param['type']) {
      case 'fix':
        $value = $param['value'];
        break;
      case 'core':
        if (isset($param['field']) && isset($productInfo[$param['field']]))
          $value = $productInfo[$param['field']];
        break;
    }
    return $value;
  }

}

?>