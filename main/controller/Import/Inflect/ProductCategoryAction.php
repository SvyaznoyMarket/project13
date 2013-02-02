<?php

namespace Controller\Import\Inflect;

class ProductCategoryAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }

    public function execute() {
        \App::logger()->debug('Exec ' . __METHOD__);

        $inflectDir = \App::config()->dataStore['url'] . 'inflect/product-category';
        if (!is_dir($inflectDir)) {
            throw new \Exception(sprintf('%s is not directory', $inflectDir));
        }

        $productCategoryRepository = \RepositoryManager::productCategory();
        $productCategoryRepository->setEntityClass('\Model\Product\Category\TreeEntity');

        $walk = function($categories) use (&$walk, $inflectDir) {
            foreach ($categories as $category) {
                /** @var \Model\Product\Category\TreeEntity $category */
                if (!$category->getName()) continue;

                echo $category->getName();

                $response = file_get_contents('http://export.yandex.ru/inflect.xml?' . http_build_query([
                    'name' => $category->getName(),
                ]));
                if (!$xml = simplexml_load_string($response)) {
                    throw new \Exception(sprintf('Невалидный xml %s', (string)$xml));
                }

                $inflect = [];
                foreach ($xml->xpath('//inflection') as $inflection) {
                    $inflect[] = (string)$inflection;
                }
                if (1 == count($inflect)) {
                    echo ' ..?';
                }

                $file = $inflectDir . '/' . $category->getId() . '.json';
                if (!file_put_contents($file, json_encode($inflect, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
                    throw new \Exception(sprintf('Неудалось записать данные в %s', $file));
                }

                echo "\n";


                if ((bool)$category->getChild()) {
                    $walk($category->getChild());
                }
            }
        };
        $walk($productCategoryRepository->getTreeCollection(\RepositoryManager::region()->getDefaultEntity()));
    }
}