<?php

#require_once 'lib/vendor/symfony/lib/helper/UrlHelper.php';
#require_once 'lib/a.php';

class ProjectSiteMapTask extends sfBaseTask
{

  /**
   * URL сайта
   * @var string
   */
  private $_fullUrl =  'http://www.enter.ru';

  /**
   * Шаблон для генерации имён файлов
   * @var type
   */
  private $_fileNameTemplate = 'web/sitemap_#NUM#.xml';
  /**
   * Имя файла - индекса
   * @var type
   */
  private $_indexFileName = 'web/sitemap.xml';

  /**
   * Имя файла, в который идён запись на данный момент
   * @var type
   */
  private $_fileName;

  /**
   * Символы, которые необходимо заменять
   * @var type
   */
  private $_replaceSymvols = array(
      'from' => array(
        '&', "'", '"', '>', '<'
        ),
      'to' => array(
        '&amp;', '&apos;', '&quot;', '&gt;', '&lt;'
        )
  );

  /**
   * Объект для маршрутизации
   * @var type
   */
  private $_routing;


  /**
   *
   * @var type Максимальное количество записей в одном файле
   */
  private $_maxNumInFile = 49999;

  /**
   * Текущее количество записей в файле
   * @var type
   */
  private $_currentNumInFile = 0;

  /**
   * Номер текущего файла
   * @var type
   */
  private $_currentFileNum = 0;


  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'main'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'project';
    $this->name             = 'sitemap';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [Project:SiteMap|INFO] task does things.
Call it with:

  [php symfony project:sitemap|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


    sfContext::createInstance($this->configuration);
    $this->_routing = $this->getRouting();

    //инициализация
    $this->_createSitemapFolder();
    file_put_contents($this->_indexFileName, '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    $this->_beginNewFile();


    $this->_putIndexUrl();
    $this->_putCategoryUrl();
    $this->_putProductUrl();
    $this->_putServiceCategoryUrl();
    $this->_putServiceUrl();
    $this->_putShopUrl();
    $this->_putStaticPagesUrl();
    $this->_putAnotherUrl();

    //закрываем все файлы
    $this->_put('</urlset>'."\n");
    file_put_contents($this->_indexFileName, '</sitemapindex>', FILE_APPEND);


  }

  private function _createSitemapFolder() {
    $pathAr = explode('/', $this->_fileNameTemplate);
    $path = "";
    for($i=0; $i<count($pathAr)-1; $i++) {
        if ($i > 0) {
            $path .= "/";
        }
        $path .= $pathAr[$i];
        if (!file_exists($path)) {
            mkdir($path)."\n";
        }
    }
    //удаляем старые файлы sitemap
    for($i=0; $i<100; $i++) {
        $name = str_replace("#NUM#", $i, $this->_fileNameTemplate);
        if (file_exists($name)) {
            unlink($name);
        }
    }
  }

  private function _beginNewFile() {
    $this->log('new file');
    $this->_currentNumInFile = 0;

    //завершим старый файл
    if (isset($this->_fileName)) {
        $this->_put('</urlset>'."\n");
    }

    //следующий номер
    $this->_currentFileNum++;
    //соответствующее имя
    $this->_fileName = str_replace('#NUM#', $this->_currentFileNum, $this->_fileNameTemplate);
    #echo $this->_fileName ."\n";
    //начинаем файл
    $this->_putNew('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
');

    //добавим в индекс новый файл
    $newFileData = '
  <sitemap>
      <loc>' . $this->_fullUrl . str_replace('web', '', $this->_fileName) . '</loc>
  </sitemap>
        ';
    file_put_contents($this->_indexFileName, $newFileData, FILE_APPEND);


  }

  private function _putIndexUrl() {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('homepage').'</loc>
  <changefreq>hourly</changefreq>
  <priority>0.8</priority>
</url>
';
        $this->_put($xmlData);
  }

  private function _putAnotherUrl() {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('callback').'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
        $this->_put($xmlData);
  }

  private function _putStaticPagesUrl() {
    $eccenseList = Doctrine_Core::getTable('Page')
            ->createQuery('p')
            ->fetchArray();
    foreach($eccenseList as $item) {
        $xmlData =
'<url>
<loc>'.$this->_generateUrl('default_show', array('page' => $item['token'])).'</loc>
<changefreq>monthly</changefreq>
<priority>0.5</priority>
</url>
';
        $this->_put($xmlData);
    }
  }

  private function _putServiceUrl() {
    $eccenseList = Doctrine_Core::getTable('Service')
            ->createQuery('s')
            ->where('s.is_active = ?', 1)
            ->orderBy('s.id')
            #->limit(50)
            ->fetchArray();
    foreach($eccenseList as $item) {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('service_show', array('service' => $item['token'])).'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
        $this->_put($xmlData);
    }
  }

  private function _putServiceCategoryUrl() {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('service_index').'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
';
        $this->_put($xmlData);

    $eccenseList = Doctrine_Core::getTable('ServiceCategory')
            ->createQuery('s')
            ->where('s.is_active = ? AND s.level = ?', array(1, 2))
            ->orderBy('s.id')
            #->limit(50)
            ->fetchArray();
    foreach($eccenseList as $item) {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('service_category', array('serviceCategory' => $item['token'])).'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
        $this->_put($xmlData);
    }
  }


  private function _putShopUrl() {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('shop').'</loc>
  <changefreq>daily</changefreq>
  <priority>0.6</priority>
</url>
';
        $this->_put($xmlData);

    $eccenseList = Doctrine_Core::getTable('Shop')
            ->createQuery('s')
            ->where('s.is_active = ?',1)
            #->limit(50)
            ->fetchArray();
    foreach($eccenseList as $item) {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('shop_show', array('shop' => $item['token'])).'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
        $this->_put($xmlData);
    }
  }


  private function _putProductUrl() {
    $productList = Doctrine_Core::getTable('Product')
            ->createQuery('pc')
            ->where('is_active = ?', 1)
            ->where('view_show = ?', 1)
            ->orderBy('pc.id')
            #->limit(50)
            ->fetchArray();
    foreach($productList as $product) {
      if (isset($product['token_prefix']) && !empty($product['token_prefix']))
      {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('productCard', array('product' => $product['token_prefix'].'/'.$product['token'])).'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
<url>
  <loc>'.$this->_generateUrl('productComment', array('product' => $product['token_prefix'].'/'.$product['token'])).'</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
';
        $this->_put($xmlData);
      }
    }
  }

  private function _putCategoryUrl() {
    $categoryList = Doctrine_Core::getTable('ProductCategory')
            ->createQuery('pc')
            ->where('is_active = ?', 1)
            ->orderBy('pc.id')
            #->limit(50)
            ->fetchArray();
    foreach($categoryList as $cat) {
        $xmlData =
'<url>
  <loc>'.$this->_generateUrl('productCatalog_category', array('productCategory' => $cat['token_prefix'] ? ($cat['token_prefix'].'/'.$cat['token']) : $cat['token'])).'</loc>
  <changefreq>daily</changefreq>
  <priority>0.8</priority>
</url>
';
        $this->_put($xmlData);
    }
  }

  private function _generateUrl($path, $data = array()) {
      $url = $this->_fullUrl .
             $this->_routing->generate($path, $data);
      $url = str_replace($this->_replaceSymvols['from'], $this->_replaceSymvols['to'], $url);
      return $url;
  }

  private function _put($data) {
        file_put_contents($this->_fileName, $data, FILE_APPEND);
        $this->_currentNumInFile += substr_count($data, '<url>');
        //если файл заполнен, надо начинать новый
       # echo $this->_currentNumInFile .'-----------'. $this->_maxNumInFile."\n";
        if ( $this->_currentNumInFile >= $this->_maxNumInFile) {
          $this->_beginNewFile();
        }
  }

  private function _putNew($data) {
        file_put_contents($this->_fileName, $data);
  }

}
