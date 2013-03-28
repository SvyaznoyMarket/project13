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
        $this->fillProductCategory();
        $this->fillProduct();
        $this->fillServiceCategory();
        $this->fillService();
        $this->fillShop();
        $this->fillPage();

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
                \App::logger()->error(sprintf('File %s does not exist', $source));
                continue;
            }

            $destination = $this->basePath . str_replace('{num}', $i, $this->fileTemplate);
            if (!copy($source, $destination)) {
                \App::logger()->error(sprintf('Can\'t copy sitemap file from %s to %s', $source, $destination));
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

        $walk = function($categories) use (&$walk) {
            foreach ($categories as $category) {
                /** @var \Model\Product\Category\TreeEntity $category */
                if (!$category->getPath()) continue;

                $this->putContent(
                    $this->router->generate('product.category', ['categoryPath' => $category->getPath()]),
                    'daily',
                    '0.8'
                );

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
                    $products = $productRepository->getIteratorByFilter($filter->dump(), [], $offset, $limit, $this->region);
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
                $services = $serviceRepository->getCollectionByCategory($category, $this->region);
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
            foreach (\RepositoryManager::shop()->getCollectionByRegion($region) as $shop) {
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
        $pages = (array)json_decode($result, true);
        foreach ($pages as $page) {
            $this->putContent(
                '/' . $page['token'],
                'monthly',
                '0.5'
            );
        }
    }

    private function putContent($url, $freq, $priority) {
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
        $count = (int)shell_exec('grep -c "<url>" ' . $this->fileName);

        if (0 == ($count % self::ENTITY_LIMIT)) echo '.';

        if (($count >= self::URL_LIMIT) || ((filesize($this->fileName) / 1048576) >= self::FILE_SIZE_LIMIT)) {
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
            \App::logger()->error(sprintf('Can\'t copy sitemap file from %s to %s', $this->fileName, $destination));
        }
        unlink($this->fileName);
        $this->fileName = null;
    }
}