<?php

class ProjectYandexMarketTask extends sfBaseTask
{
  
  private $_companyData = array(
      'name' => 'Enter.ru',
      'company' => 'Enter.ru',
      'url' => 'http://www.enter.ru',
      'email' => 'enter@enter.ru'
  );

  
  private $_xmlFolder ='web/xml';  
  
  /**
   * Название файла дли экспорта
   * @var string 
   */  
  private $_xmlFileName ='ya_market.xml';  
  
  /**
   * Id региона, для которого выгружаем данные
   * @var integer 
   */
  private $_regionId;       //TO DO
  
  /**
   * Флаг - выгружать ли товары НЕ в наличии
   * @var boolean 
   */
  private $_exportNotInStock = false;
  
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
  
  /**
   * Флаг - выгжужать  url категорий
   * @var boolean 
   */
  private $_uploadCategotyUrl = true;
  
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
      'delivery',
      'description',
      'local_delivery_cost',
  );
  
  /**
   * Дополнительные параметры в выгрузке продуктов
   * @var array
   * для каждого элемента:
   *   - name - имя для выгрузки
   *   - type = core/fix - из ядра, либо фиксированное значение
   *   - field - название_параметра_в_core (только для type==core)
   *   - value - значение (только для type==fix)
   *  Пример:
      array(
          'name' => 'test',
          'type' => 'fix',
          'value' => 88
      ),
      array(
          'name' => 'article-value',
          'type' => 'core',
          'field' => 'article'
      ) 
    * */
  private $_additionalParams = array();
  
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
  private $_portionToLoadProduct = 100;
    
  /**
   * Рутовые категории, из которых выгружаем в разные файлы
   * @var type 
   */
  private $_globalCatList = array(
      array(
          'name' => 'export_realweb.xml',
          'list' => array(6,5,8,9)
          ),   
      array(
          'name' => 'export_mgcom.xml',
          'list' => array(3,2,1,4,7)
          ),           
  );
  
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

    $this->namespace        = 'Project';
    $this->name             = 'YandexMarket';
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
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    if (!file_exists($this->_xmlFolder)) mkdir($this->_xmlFolder);
    
    $config = sfConfig::getAll();
    $this->_imageUrlsConfig = $config['app_product_photo_url'];
    #var_dump($this->_imageUrlsConfig);
    #exit();
    
    //генерируем файл со всеми товарами
    $this->_xmlFilePath = $this->_xmlFolder . '/' . $this->_xmlFileName;  //'/mnt/hgfs/httpdFiles/'.
    $this->_xmlGenerateItself();
    
    $this->_generateCatList();    
    
    //генерируем файлы с определёнными категориями
    
    if (count($this->_globalCatList)>0)
    foreach($this->_globalCatList as $partInfo){
        //заполняем массив категорий
        $this->_categoryList = array();
        foreach($partInfo['inner'] as $catId){
            $this->_categoryList[$catId] = array();
        }
        $this->_xmlFilePath = $this->_xmlFolder . '/' . $partInfo['name'];
        //выполняем саму генарацию
        $this->_xmlGenerateItself();
    }
    #print_r($this->_categoryList);
    #exit();
    
  }
  
    private function _xmlGenerateItself(){
        //корневой каталог
        $this->_xmlResult = new SimpleXMLElement("<yml_catalog date='".date("Y-m-d H:n")."'></yml_catalog>");
        file_put_contents($this->_xmlFilePath,'<?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd">');
        file_put_contents($this->_xmlFilePath,"<yml_catalog  date='".date("Y-m-d H:n")."'>",FILE_APPEND);
        //базовое
        $this->_setShop();
        file_put_contents($this->_xmlFilePath,'</yml_catalog>',FILE_APPEND);      
    }
  
  public function _generateCatList(){
      //глобальные списки глобальных категорий
      foreach($this->_globalCatList as $k => $catList){
            $idList = array();
            $catList = Doctrine_Core::getTable('ProductCategory')
                    ->createQuery('pc')
                    ->where('pc.root_id IN ('.implode(',',$catList['list']) .')'  )
                    ->fetchArray();
                    ;
            foreach($catList as $cat) $idList[] = $cat['id'];
            $this->_globalCatList[$k]['inner'] = $idList;
          
      }
    #  exit();
  }
  
  
  /**
   * Дабавляет базовую информацию о магазине 
   */
  private function _setShop(){
    file_put_contents($this->_xmlFilePath,'<shop>',FILE_APPEND);

    $this->_xmlResultShop = $this->_xmlResult->addChild('shop');    
    $next = $this->_xmlResultShop->addChild('name',$this->_companyData['name']);
    file_put_contents($this->_xmlFilePath,$next->asXML(),FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('company',$this->_companyData['company']);
    file_put_contents($this->_xmlFilePath,$next->asXML(),FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('url',$this->_companyData['url']);
    file_put_contents($this->_xmlFilePath,$next->asXML(),FILE_APPEND);
    $next = $this->_xmlResultShop->addChild('email',$this->_companyData['email']);
    file_put_contents($this->_xmlFilePath,$next->asXML(),FILE_APPEND);

    //file_put_contents($this->_xmlFilePath,$this->_xmlResultShop->asXML(),FILE_APPEND);
    
    //валюты
    $this->_setCurrencyList();
    //категории товаров
    $this->_setCategoryList();
    //товары
    $this->_setOffersList();
    
    file_put_contents($this->_xmlFilePath,'</shop>',FILE_APPEND);
      
  }
  
  /**
   * Добавляет в xml список валют (единственную валюту - рубль)
   */
  private function _setCurrencyList(){
    $currencies = $this->_xmlResultShop->addChild('currencies');
    //валюта - единственная
    $currency = $currencies->addChild('currency');
    $currency->addAttribute('id', 'RUR');
    $currency->addAttribute('rate', '1');      
    file_put_contents($this->_xmlFilePath,$currencies->asXML(),FILE_APPEND);
  }
  
  /**
   * Добавляет в xml список категорий товаров
   */
  private function _setCategoryList(){
    $categoryList = Doctrine_Core::getTable('ProductCategory')
            ->createQuery('pc')
            ->select('pc.*') 
            ->whereIn('id',  array_keys($this->_categoryList))          
            ->orderBy('pc.id')
            #->limit(50)
            ->fetchArray();
    foreach($categoryList as $cat){
        $catIdToCoreId[ $cat['core_id'] ] = $cat['id'];
    }
    $cats = $this->_xmlResultShop->addChild('categories');
    
    file_put_contents($this->_xmlFilePath,'<categories>',FILE_APPEND);
    $numInRound = 0;
    $currentXml = "";
    foreach($categoryList as $categoryInfo){
        $cat = $cats->addChild('category',$categoryInfo['name']);
        $cat->addAttribute('id',$categoryInfo['id']);
        if ($categoryInfo['core_parent_id'] && isset($catIdToCoreId[ $categoryInfo['core_parent_id'] ])) $cat->addAttribute('parentId', $catIdToCoreId[ $categoryInfo['core_parent_id'] ]);
        //если нужно добавить url
        //хак!! для файла ya_market.xml не выгразуем пути
        if ($this->_uploadCategotyUrl && strpos($this->_xmlFilePath, 'ya_market.xml') === false  ){
            $cat->addAttribute('url',$this->_companyData['url'].'/catalog/'.$categoryInfo['token'].'/');            
        }
        
        $numInRound++;
        //записываем в файл порциями по 200 штук
        //чтобы не открывать файл слишком много раз
        if ($numInRound>=$this->_portionToLoadCategory){
            $numInRound = 0;
            file_put_contents($this->_xmlFilePath,$currentXml,FILE_APPEND);            
            $currentXml = "";
        }        
        $currentXml .= $cat->asXML();           
    }  
    
    file_put_contents($this->_xmlFilePath,$currentXml.'</categories>',FILE_APPEND);
     
  }  
  
  /**
   * Добавляет в XML список товаров
   */
  private function _setOffersList(){
      
    //узел продуктов  
    $offers = $this->_xmlResultShop->addChild('offers');    

    $categoryLimitExist = false;
    if (count($this->_categoryList)>0) $categoryLimitExist = true;
    //учитываем список категорий, если он задан, для будующей выборки 
    if ($categoryLimitExist){
        foreach($this->_categoryList as $catId => $catInfo){
            $catIdList[] = $catId;
        }
        $catIdListString = implode(',',$catIdList);
    }  
    
    //делаем выборку товаров
    $offersList = Doctrine_Core::getTable('Product')
            ->createQuery('p')
            ->distinct()
            ->select('p.*,pcr.product_category_id,cr.name,price.price,type.name,photo.resource') 
            ->leftJoin('p.ProductCategoryProductRelation pcr on p.id=pcr.product_id ')      //категория     
            ->leftJoin('p.Photo photo on p.id=photo.product_id ')           //фото
           # ->leftJoin('p.Type type on p.type_id=type.id ')                 //тип
            ->leftJoin('p.Creator cr on cr.id=p.creator_id ')               //производитель
            ->leftJoin('p.ProductPrice price on price.product_id=p.id ')    //цена    
            ;
    //если нужно выгрузить только те, что есть в наличии
    if (!$this->_exportNotInStock){
        $offersList->where('is_instock=?',1);
    }
    //если есть ограничения по категориям
    if (isset($catIdListString)){
        $offersList
            ->where('pcr.product_category_id IN ('.$catIdListString.')');
    }
    $offersList = $offersList
            ->orderBy('p.rating DESC')
           # ->limit(50)
            ->fetchArray();
    #echo $offersList;
    #print_r($offersList);
    
    $numInRound = 0;
    $currentXml = "";
    file_put_contents($this->_xmlFilePath,'<offers>',FILE_APPEND);
    
    foreach($offersList as $offerInfo){
        
        try{
            
            //DEPRICATED! не используем объект, так как с ним получается очень долго.
            //получаем объект продукта
          #  $prodObject = ProductTable::getInstance()->getById($offerInfo['id']);
          #  if (!$prodObject) continue;


            //если ограничения по категориям существуют - проверяем каждый товар
            //возможно его цена меньше разрешенной
            //либо достаточное для данной категории количество уже набрано
            //это не было учтено при выборке!
            if ($categoryLimitExist){
                $productCatId = $offerInfo['ProductCategoryProductRelation'][0]['product_category_id'];
                //если не проходим ограничения по цене
                if (isset($this->_categoryList[ $productCatId ]['min_price']) && $offerInfo['ProductPrice'][0]['price']<$this->_categoryList[ $productCatId ]['min_price'] ){
                    continue;
                }
                //проверяем максимальное количество товаров для этой категории
                if (isset($this->_categoryList[ $productCatId ]['max_num']) ){
                    //если нужное количество уже набрали
                    if (isset($resultAddedList[ $productCatId ]) && count($resultAddedList[ $productCatId ])>=$this->_categoryList[ $productCatId ]['max_num']){
                        continue;                    
                    }
                    //считаем, сколько уже набранно
                    $resultAddedList[ $productCatId ][] = $offerInfo['id'];
                }            
            }


            //создаём узел продукта и устанавливаем значения атрибутов
            $offer = $offers->addChild('offer');
            $offer->addAttribute('id',$offerInfo['id']);
            if ($offerInfo['is_instock']) $inStock = 'true';
            else $inStock = 'false';
            $offer->addAttribute('available',$inStock);
            $offer->addAttribute('type','vendor.model');

            //основные параметры
            foreach($this->_uploadParamsList as $param){
                $value = $this->_getPropValueByCode($offerInfo,$param);
                if ($value) $offer->$param = $value;
            }            
            //дополнительные параметры
            foreach($this->_additionalParams as $addParam){
                $value = $this->_getAdditionalPropValueByCode($offerInfo,$addParam);
                if ($value) $offer->$addParam['name'] = $value;
            }

        }
        
        catch(Exception $e){            
            #echo 'eeroor--'.$e->getMessage().$e->getFile().'=='.$e->getLine().'        ';
            continue;
        }
        
        $numInRound++;
        //записываем в файл порциями по 100 штук
        if ($numInRound>=$this->_portionToLoadProduct){
            $numInRound = 0;
            file_put_contents($this->_xmlFilePath,$currentXml,FILE_APPEND);            
            $currentXml = "";
        }        
        $currentXml .= $offer->asXML();            
        

    }
    file_put_contents($this->_xmlFilePath,$currentXml.'</offers>',FILE_APPEND);
    
    
    
      
  }
  
  
  private function _getPropValueByCode($offerInfo,$code){
        $value = "";
        switch ($code){
            case 'url':
                $value = $this->_companyData['url'].'/products/'.$offerInfo['token'];
                break;
            case 'price':
                if (isset($offerInfo['ProductPrice'][0])) $value = $offerInfo['ProductPrice'][0]['price'];
                break;
            case 'categoryId':
                if (isset($offerInfo['ProductCategoryProductRelation'][0]['product_category_id'])) 
                    $value = $offerInfo['ProductCategoryProductRelation'][0]['product_category_id'];
                break;
            case 'picture':
                if (isset($offerInfo['Photo']) && isset($offerInfo['Photo'][0]) && isset($offerInfo['Photo'][0]['resource'])) 
                    $value =  $this->_imageUrlsConfig[3] . $offerInfo['Photo'][0]['resource'];
                break;
            case 'typePrefix':
                #$value = $offerInfo['Type']['name'];
                $value = $offerInfo['prefix'];
                break;
            case 'vendor':
                $value = $offerInfo['Creator']['name'];
                break;
            case 'model':
                $value = trim( str_replace(array($offerInfo['prefix'],$offerInfo['Creator']['name']),'',$offerInfo['name']) );
                break;
            case 'name':
                $value = '';//$prodObject->getName();
                break;
            case 'pickup':
                $value = 'true';
                break;
            case 'delivery':
                $value = 'true';
                break;
            case 'description':
                $value = $offerInfo['description'];
                break;
            case 'local_delivery_cost':
                $value = 0;         //TO DO. не известно на данный момент
                break;
        }
        return $value;     
  }
  
  /** DEPRECATED
  private function _getPropValueByCode($prodObject,$code){
        $value = "";
        switch ($code){
            case 'url':
                $value = $this->_companyData['url'].$prodObject->getUrl();
                break;
            case 'price':
                $value = $prodObject->getPrice();
                break;
            case 'categoryId':
                $value = $prodObject->getMainCategoryId();
                break;
            case 'picture':
                $value = $prodObject->getMainPhotoUrl();
                break;
            case 'typePrefix':
                $value = $prodObject->getType();
                break;
            case 'vendor':
                $value = $prodObject->getCreator();
                break;
            case 'model':
                $value = $prodObject->getName();
                break;
            case 'name':
                $value = '';//$prodObject->getName();
                break;
            case 'pickup':
                $value = 'true';
                break;
            case 'delivery':
                $value = 'true';
                break;
            case 'description':
                $value = $prodObject->getDescription();
                break;
            case 'local_delivery_cost':
                $value = 0;         //TO DO. не известно на данный момент
                break;
        }
        return $value;     
  }
   */
  
  private function _getAdditionalPropValueByCode($productInfo,$param){
        $value = '';
        switch ($param['type']){
            case 'fix':
                $value = $param['value'];
                break;
            case 'core':
                if (isset($param['field']) && isset($productInfo[ $param['field']  ]))
                    $value = $productInfo[ $param['field']  ];
                break;
        }      
        return $value;
  }
  
}
?>