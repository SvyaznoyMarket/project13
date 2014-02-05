<?php

namespace Controller\Command;

class SitemapAction {
    const URL_LIMIT = 49999; // http://ru.wikipedia.org/wiki/Sitemaps
    const FILE_SIZE_LIMIT = 10; // Mb
    const ENTITY_LIMIT = 100;
    const URL_PREFIX = 'http://www.enter.ru';

    private $basePath;
    private $tempPath;
    private $indexFileName;
    private $fileTemplate;

    private $router;
    private $region;
    private $fileName;
    private $fileCount = 0;

    public function __construct() {
        $this->router = \App::router();
        $this->region = \RepositoryManager::region()->getDefaultEntity();

        $this->basePath = \App::config()->webDir . '/';
        $this->tempPath = (sys_get_temp_dir() ?: '/tmp') . '/';
        $this->indexFileName = $this->basePath . '/sitemap.xml';
        $this->fileTemplate = 'sitemap_{num}.xml';
    }

    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $this->fillHomepage();
        \App::logger()->info('Sitemap: главная страница готова', ['sitemap']);
        $this->fillProductCategory();
        \App::logger()->info('Sitemap: каталог товаров готов', ['sitemap']);
        $this->fillProduct();
        \App::logger()->info('Sitemap: товары готовы', ['sitemap']);
        //$this->fillServiceCategory();
        //\App::logger()->info('Sitemap: категории услуг готовы', ['sitemap']);
        //$this->fillService();
        //\App::logger()->info('Sitemap: услуги готовы', ['sitemap']);
        $this->fillShop();
        \App::logger()->info('Sitemap: магазины готовы', ['sitemap']);
        $this->fillPage();
        \App::logger()->info('Sitemap: информационные страницы готовы', ['sitemap']);

        $this->closeContent();

        $this->replace();

        echo "\n";
    }

    private function replace() {
        // удаляем старые файлы
        $file = $this->indexFileName;
        if (is_file($file)) {
            unlink($file);
        }

        for ($i = 1; $i < 1000; $i++) {
            $file = $this->basePath . str_replace('{num}', $i, $this->fileTemplate);
            if (is_file($file)) {
                unlink($file);
            }
        }

        // создаем новые файлы
        $files = [];
        for ($i = 1; $i <= $this->fileCount; $i++) {
            $source = $this->basePath . str_replace('{num}', $i, $this->fileTemplate) . '.new';
            if (!is_file($source)) {
                \App::logger()->error(sprintf('File %s does not exist', $source), ['sitemap']);
                continue;
            }

            $destination = $this->basePath . str_replace('{num}', $i, $this->fileTemplate);
            if (!copy($source, $destination)) {
                \App::logger()->error(sprintf('Can\'t copy sitemap file from %s to %s', $source, $destination), ['sitemap']);
                continue;
            }

            unlink($source);

            $files[] = basename($destination);
        }

        if ((bool)$files) {
            file_put_contents($this->indexFileName,
                '<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
'
            );

            foreach ($files as $file) {
                file_put_contents($this->indexFileName, '
  <sitemap>
      <loc>' . self::URL_PREFIX . '/' . $file . '</loc>
  </sitemap>
', FILE_APPEND);
            }

            file_put_contents($this->indexFileName,
'
</sitemapindex>
',
                FILE_APPEND);
        }
    }

    private function fillHomepage() {
        $this->putContent(
            $this->router->generate('homepage'),
            'hourly',
            '1'
        );
    }

    private function fillProductCategory() {
        $productCategoryRepository = \RepositoryManager::productCategory();
        $productCategoryRepository->setEntityClass('\Model\Product\Category\TreeEntity');

        $defaultRegion = \RepositoryManager::region()->getDefaultEntity();

        $walk = function($categories) use (&$walk, &$defaultRegion) {
            foreach ($categories as $category) {
                /** @var \Model\Product\Category\TreeEntity $category */
                if (!$category->getPath()) continue;

                $this->putContent(
                    $this->router->generate('product.category', ['categoryPath' => $category->getPath()]),
                    'daily',
                    '0.8'
                );

                // генерируем категория+бренд страницы только для нерутовых категорий
                if ($category->getLevel() > 1) {
                    try {
                        for ($i = 0; $i < 10; $i++) {
                            foreach (\RepositoryManager::brand()->getCollectionByCategory($category, $i * 100, 100) as $brand) {
                                if (!$brand->getToken()) {
                                    \App::logger()->warn(sprintf('Бренд #%s не содержит токена', $brand->getId()));
                                    continue;
                                }
                                $this->putContent(
                                    $this->router->generate('product.category.brand', [
                                        'brandToken'   => $brand->getToken(),
                                        'categoryPath' => $category->getPath(),
                                    ]),
                                    'daily',
                                    '0.8'
                                );
                            }
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error($e, ['sitemap']);
                    }
                }

                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk($productCategoryRepository->getTreeCollection($this->region));
    }

    private function fillProduct() {
        $productCategoryRepository = \RepositoryManager::productCategory();
        $productCategoryRepository->setEntityClass('\Model\Product\Category\TreeEntity');

        $productRepository = \RepositoryManager::product();
        $productRepository->setEntityClass('\Model\Product\BasicEntity');

        /** @param \Model\Product\Category\TreeEntity[] $categories */
        $walk = function($categories) use (&$walk, $productRepository) {
            $limit = self::ENTITY_LIMIT;
            foreach ($categories as $category) {
                $filter = new \Model\Product\Filter([]);
                $filter->setCategory($category);

                $offset = 0;
                while (($category->getGlobalProductCount() - ($offset + $limit)) > 0) {
                    try {
                        $productIds = [];
                        $productCount = 0;
                        $productRepository->prepareIteratorByFilter(
                            $filter->dump(),
                            [],
                            $offset,
                            $limit,
                            $this->region,
                            function($data) use (&$productIds, &$productCount) {
                                if (isset($data['list'][0])) $productIds = $data['list'];
                                if (isset($data['count'])) $productCount = (int)$data['count'];
                            }
                        );
                        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

                        $products = [];
                        if ((bool)$productIds) {
                            $productRepository->prepareCollectionById($productIds, $this->region, function($data) use (&$products) {
                                foreach ($data as $item) {
                                    $products[] = new \Model\Product\CompactEntity($item);
                                }
                            });

                            $scoreData = [];
                            \RepositoryManager::review()->prepareScoreCollection($productIds, function($data) use (&$scoreData) {
                                if (isset($data['product_scores'][0])) {
                                    $scoreData = $data;
                                }
                            });
                        }
                        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

                        \RepositoryManager::review()->addScores($products, $scoreData);

                        $products = new \Iterator\EntityPager($products, $productCount);

                    } catch (\Exception $e) {
                        \App::logger()->error($e, ['sitemap']);
                        continue;
                    }

                    foreach ($products as $product) {
                        /** @var \Model\Product\BasicEntity $product */
                        if (!$product->getPath()) continue;

                        $this->putContent(
                            $this->router->generate('product', ['productPath' => $product->getPath()]),
                            'daily',
                            '0.8'
                        );
                    }

                    $offset += $limit;
                }

                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk($productCategoryRepository->getTreeCollection($this->region));
    }

    private function fillServiceCategory() {
        $walk = function($categories) use (&$walk) {
            foreach ($categories as $category) {
                /** @var \Model\Product\Service\Category\Entity $category */
                if (!$category->getToken()) continue;

                $this->putContent(
                    $this->router->generate('service.category', ['categoryToken' => $category->getToken()]),
                    'monthly',
                    '0.6'
                );

                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk(\RepositoryManager::serviceCategory()->getCollection($this->region));
    }

    private function fillService() {
        $serviceRepository = \RepositoryManager::service();
        $walk = function($categories) use (&$walk, $serviceRepository) {
            /** @var $category \Model\Product\Service\Category\Entity */
            foreach ($categories as $category) {
                try {
                    $services = $serviceRepository->getCollectionByCategory($category, $this->region);
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['sitemap']);
                    continue;
                }

                foreach ($services as $service) {
                    if (!$service->getToken()) continue;

                    /** @var $service \Model\Product\Service\Entity */
                    $this->putContent(
                        $this->router->generate('service.show', ['serviceToken' => $service->getToken()]),
                        'monthly',
                        '0.5'
                    );
                }

                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk(\RepositoryManager::serviceCategory()->getCollection($this->region));
    }

    private function fillShop() {
        foreach (\RepositoryManager::region()->getShopAvailableCollection() as $region) {
            try {
                $shops = \RepositoryManager::shop()->getCollectionByRegion($region);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['sitemap']);
                continue;
            }

            foreach ($shops as $shop) {
                $this->putContent(
                    $this->router->generate('shop.show', ['regionToken' => $region->getToken(), 'shopToken' => $shop->getToken()]),
                    'daily',
                    '0.6'
                );
            }
        }
    }

    private function fillPage() {
        $result = \App::contentClient()->query('api/page/list/');

        try {
            $pages = (array)json_decode($result, true);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['sitemap']);
            $pages = [];
        }
        foreach ($pages as $page) {
            $this->putContent(
                '/' . $page['token'],
                'monthly',
                '0.5'
            );
        }
    }

    private function putContent($url, $freq, $priority) {
        static $count = 0;

        $count++;

        $content =
            '<url>
  <loc>' . self::URL_PREFIX . str_replace(['&', "'", '"', '>', '<'], ['&amp;', '&apos;', '&quot;', '&gt;', '&lt;'], $url) . '</loc>
  <changefreq>' . $freq . '</changefreq>
  <priority>' . $priority . '</priority>
</url>
';

        if (!$this->fileName) {
            $this->fileName = $this->tempPath . str_replace('{num}', ++$this->fileCount, $this->fileTemplate);
            if (file_exists($this->fileName)) {
                unlink($this->fileName);
            }

            file_put_contents($this->fileName,
'<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
'
            );

            echo "\n" . $this->fileName;
        }

        file_put_contents($this->fileName, $content, FILE_APPEND);

        if (0 == ($count % self::ENTITY_LIMIT)) echo '.';

        if (($count >= self::URL_LIMIT) || ((filesize($this->fileName) / 1048576) >= self::FILE_SIZE_LIMIT)) {
            $count = 0;
            $this->closeContent();
        }
    }

    private function closeContent() {
        if (!$this->fileName) {
            return;
        }

        file_put_contents($this->fileName, '</urlset>' . "\n", FILE_APPEND);

        $destination = $this->basePath . basename($this->fileName) . '.new';
        if (!copy($this->fileName, $destination)) {
            \App::logger()->error(sprintf('Can\'t copy sitemap file from %s to %s', $this->fileName, $destination), ['sitemap']);
        }
        unlink($this->fileName);
        $this->fileName = null;
    }
}