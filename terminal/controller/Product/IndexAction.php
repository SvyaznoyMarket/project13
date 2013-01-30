<?php

namespace Terminal\Controller\Product;

class IndexAction {
    public function execute($productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        \RepositoryManager::product()->prepareEntityById($productId, $region, function($data) use (&$product) {
            $data = reset($data);
            if ((bool)$data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар #%s не найден.', $productId));
        }

        $accessoryIds =  array_slice($product->getAccessoryId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $relatedIds = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $partIds = array();

        foreach ($product->getKit() as $part) {
            $partIds[] = $part->getId();
        }

        $accessories = array_flip($accessoryIds);
        $related = array_flip($relatedIds);
        $kit = array_flip($partIds);

        if ((bool)$accessoryIds || (bool)$relatedIds || (bool)$partIds) {
            try {
                $products = \RepositoryManager::product()->getCollectionById(array_merge($accessoryIds, $relatedIds, $partIds));
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $products = array();
                $accessories = array();
                $related = array();
                $kit = array();
            }

            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) $accessories[$item->getId()] = $item;
                if (isset($related[$item->getId()])) $related[$item->getId()] = $item;
                if (isset($kit[$item->getId()])) $kit[$item->getId()] = $item;
            }
        }

        $page = new \Terminal\View\Product\IndexPage();
        $page->setParam('product', $product);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);

        return new \Http\Response($page->show());
    }
}
