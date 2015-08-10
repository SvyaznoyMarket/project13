<?php

namespace Controller\Product;

class StockAction {
    public function execute($productPath, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            
            $client->execute();
        }

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            $data = reset($data);
            if ((bool)$data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        $page = new \View\Product\StockPage();
        $page->setParam('product', $product);

        return new \Http\Response($page->show());
    }
}