<?php

namespace Controller\Command;

class SitemapAction {
    /**
     * @var string URL сайта
     */
    private $_fullUrl = 'http://www.enter.ru';

    /**
     * @var string Шаблон для генерации имён файлов
     */
    private $fileNameTemplate = 'web/sitemap_#NUM#.xml';
    /**
     * @var string Имя файла - индекса
     */
    private $indexFileName = 'web/sitemap.xml';

    /**
     * @var string Имя файла, в который идён запись на данный момент
     */
    private $fileName;

    /**
     * @var array Символы, которые необходимо заменять
     */
    private $replaceSymbols = array(
        'from' => array('&', "'", '"', '>', '<'),
        'to' => array('&amp;', '&apos;', '&quot;', '&gt;', '&lt;')
    );

    /**
     * @var \Routing\Router Объект для маршрутизации
     */
    private $router;

    /**
     *
     * @var int Максимальное количество записей в одном файле
     */
    private $maxNumInFile = 49999;

    /**
     * @var int Текущее количество записей в файле
     */
    private $currentNumInFile = 0;

    /**
     * @var int Номер текущего файла
     */
    private $currentFileNum = 0;

    public function __construct() {
        $this->router = \App::router();
    }

    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        //инициализация
        $this->createSitemapFolder();
        file_put_contents($this->indexFileName, '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
        $this->beginNewFile();


        $this->putIndexUrl();
        $this->putCategoryUrl();
        $this->putProductUrl();
        $this->putServiceCategoryUrl();
        $this->putServiceUrl();
        $this->putShopUrl();
        $this->putStaticPagesUrl();
        $this->putAnotherUrl();

        //закрываем все файлы
        $this->put('</urlset>' . "\n");
        file_put_contents($this->indexFileName, '</sitemapindex>', FILE_APPEND);
    }

    private function createSitemapFolder() {
        $pathAr = explode('/', $this->fileNameTemplate);
        $path = "";
        for ($i = 0; $i < count($pathAr) - 1; $i++) {
            if ($i > 0) {
                $path .= "/";
            }
            $path .= $pathAr[$i];
            if (!file_exists($path)) {
                mkdir($path);
            }
        }
        //удаляем старые файлы sitemap
        for ($i = 0; $i < 100; $i++) {
            $name = str_replace("#NUM#", $i, $this->fileNameTemplate);
            if (file_exists($name)) {
                unlink($name);
            }
        }
    }

    private function beginNewFile() {
        $this->currentNumInFile = 0;

        //завершим старый файл
        if (isset($this->fileName)) {
            $this->put('</urlset>' . "\n");
        }

        //следующий номер
        $this->currentFileNum++;
        //соответствующее имя
        $this->fileName = str_replace('#NUM#', $this->currentFileNum, $this->fileNameTemplate);
        #echo $this->_fileName ."\n";
        //начинаем файл
        $this->putNew('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
');

        //добавим в индекс новый файл
        $newFileData = '
  <sitemap>
      <loc>' . $this->_fullUrl . str_replace('web', '', $this->fileName) . '</loc>
  </sitemap>
        ';
        file_put_contents($this->indexFileName, $newFileData, FILE_APPEND);
    }

    private function putIndexUrl() {
        $xmlData = '<url>
  <loc>' . $this->generateUrl('homepage') . '</loc>
  <changefreq>hourly</changefreq>
  <priority>0.8</priority>
</url>
';
        $this->put($xmlData);
    }

    private function putAnotherUrl() {
        $xmlData = '<url>
  <loc>' . $this->generateUrl('callback') . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
        $this->put($xmlData);
    }

    private function putStaticPagesUrl() {
        // TODO: ждем решения задачи CON-60
        $pages = Doctrine_Core::getTable('Page')->createQuery('p')->fetchArray();
        foreach ($pages as $item) {
            $xmlData = '<url>
<loc>' . $this->generateUrl('default_show', array('page' => $item['token'])) . '</loc>
<changefreq>monthly</changefreq>
<priority>0.5</priority>
</url>
';
            $this->put($xmlData);
        }
    }

    private function putServiceUrl() {
        $services = Doctrine_Core::getTable('Service')->createQuery('s')->where('s.is_active = ?', 1)->orderBy('s.id') #->limit(50)
            ->fetchArray();
        foreach ($services as $item) {
            $xmlData = '<url>
  <loc>' . $this->generateUrl('service_show', array('service' => $item['token'])) . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
            $this->put($xmlData);
        }
    }

    private function putServiceCategoryUrl() {
        $xmlData = '<url>
  <loc>' . $this->generateUrl('service_index') . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
';
        $this->put($xmlData);

        $eccenseList = Doctrine_Core::getTable('ServiceCategory')->createQuery('s')->where('s.is_active = ? AND s.level = ?', array(1, 2))->orderBy('s.id') #->limit(50)
            ->fetchArray();
        foreach ($eccenseList as $item) {
            $xmlData = '<url>
  <loc>' . $this->generateUrl('service_category', array('serviceCategory' => $item['token'])) . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
            $this->put($xmlData);
        }
    }


    private function putShopUrl() {
        $xmlData = '<url>
  <loc>' . $this->generateUrl('shop') . '</loc>
  <changefreq>daily</changefreq>
  <priority>0.6</priority>
</url>
';
        $this->put($xmlData);

        $eccenseList = Doctrine_Core::getTable('Shop')->createQuery('s')->where('s.is_active = ?', 1) #->limit(50)
            ->fetchArray();
        foreach ($eccenseList as $item) {
            $xmlData = '<url>
  <loc>' . $this->generateUrl('shop_show', array('shop' => $item['token'])) . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.5</priority>
</url>
';
            $this->put($xmlData);
        }
    }


    private function putProductUrl() {
        $productList = Doctrine_Core::getTable('Product')->createQuery('pc')->where('is_active = ?', 1)->where('view_show = ?', 1)->orderBy('pc.id') #->limit(50)
            ->fetchArray();
        foreach ($productList as $product) {
            if (isset($product['token_prefix']) && !empty($product['token_prefix'])) {
                $xmlData = '<url>
  <loc>' . $this->generateUrl('productCard', array('product' => $product['token_prefix'] . '/' . $product['token'])) . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
<url>
  <loc>' . $this->generateUrl('productComment', array('product' => $product['token_prefix'] . '/' . $product['token'])) . '</loc>
  <changefreq>monthly</changefreq>
  <priority>0.6</priority>
</url>
';
                $this->put($xmlData);
            }
        }
    }

    private function putCategoryUrl() {
        $categoryList = Doctrine_Core::getTable('ProductCategory')->createQuery('pc')->where('is_active = ?', 1)->orderBy('pc.id') #->limit(50)
            ->fetchArray();
        foreach ($categoryList as $cat) {
            $xmlData = '<url>
  <loc>' . $this->generateUrl('productCatalog_category', array('productCategory' => $cat['token_prefix'] ? ($cat['token_prefix'] . '/' . $cat['token']) : $cat['token'])) . '</loc>
  <changefreq>daily</changefreq>
  <priority>0.8</priority>
</url>
';
            $this->put($xmlData);
        }
    }

    private function generateUrl($path, $data = array()) {
        $url = $this->_fullUrl . $this->router->generate($path, $data);
        $url = str_replace($this->replaceSymbols['from'], $this->replaceSymbols['to'], $url);

        return $url;
    }

    private function put($data) {
        file_put_contents($this->fileName, $data, FILE_APPEND);
        $this->currentNumInFile += substr_count($data, '<url>');
        //если файл заполнен, надо начинать новый
        # echo $this->_currentNumInFile .'-----------'. $this->_maxNumInFile."\n";
        if ($this->currentNumInFile >= $this->maxNumInFile) {
            $this->beginNewFile();
        }
    }

    private function putNew($data) {
        file_put_contents($this->fileName, $data);
    }
}