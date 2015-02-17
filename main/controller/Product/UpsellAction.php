<?php

namespace Controller\Product;

class UpsellAction {
    /**
     * @param string        $productToken
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            $regionConfig = [];
            \App::dataStoreClient()->addQuery("region/{$user->getRegionId()}.json", [], function($data) use (&$regionConfig) {
                if((bool)$data) {
                    $regionConfig = $data;
                }
            });

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            // выполнение 1-го пакета запросов
            $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

            $regionEntity = $user->getRegion();
            if ($regionEntity instanceof \Model\Region\Entity) {
                if (array_key_exists('reserve_as_buy', $regionConfig)) {
                    $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
                }
                $user->setRegion($regionEntity);
            }
        }

        $region = $user->getRegion();

        /** @var $product \Model\Product\Entity */
        $product = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            $data = is_array($data) ? reset($data) : null;
            if ($data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);

        $accessories = array_flip($accessoriesId);
        $related = array_flip($relatedId);

        if ((bool)$accessoriesId || (bool)$relatedId) {
            try {
                $products = \RepositoryManager::product()->getCollectionById(array_merge($accessoriesId, $relatedId));
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $products = [];
                $accessories = [];
                $related = [];
            }

            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) $accessories[$item->getId()] = $item;
                if (isset($related[$item->getId()])) $related[$item->getId()] = $item;
            }
        }

        $page = new \View\Product\UpsellPage();
        $page->setParam('product', $product);
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);

        return new \Http\Response($page->show());
    }

}