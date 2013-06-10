<?php

namespace Mobile\Controller\ProductCategory;

class Action {
    public function category($categoryPath) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            if ($user->getRegionId()) {
                \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                    $data = reset($data);
                    if ((bool)$data) {
                        \App::user()->setRegion(new \Model\Region\Entity($data));
                    }
                });
            }
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        /** @var $region \Model\Region\Entity|null */
        $region = \Controller\ProductCategory\Action::isGlobal() ? null : \App::user()->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем категорию по токену
        /** @var $category \Model\Product\Category\Entity */
        $category = null;
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }

        // подготовка 3-го пакета запросов

        // запрашиваем дерево категорий
        \RepositoryManager::productCategory()->prepareEntityBranch($category, $region);

        // выполнение 3-го пакета запросов
        $client->execute();

        $page = new \Mobile\View\ProductCategory\BranchPage();
        $page->setParam('category', $category);

        return new \Http\Response($page->show());
    }
}