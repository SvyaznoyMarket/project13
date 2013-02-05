<?php

namespace Controller\Promo;

class IndexAction {
    public function execute($promoToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        $promo = \RepositoryManager::promo()->getEntityByToken($promoToken);
        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $promoToken));
        }

        // товары, услуги, категории
        /** @var $productsById \Model\Product\BasicEntity[] */
        $productsById = [];
        /** @var $productsById \Model\Product\Service\Entity[] */
        $servicesById = [];
        /** @var $productsById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        foreach ($promo->getImage() as $promoImage) {
            switch ($promoImage->getAction()) {
                case \Model\Promo\Image\Entity::ACTION_PRODUCT:
                    foreach ($promoImage->getItem() as $id) $productsById[$id] = null;
                    break;
                case \Model\Promo\Image\Entity::ACTION_PRODUCT_CATEGORY:
                    foreach ($promoImage->getItem() as $id) $categoriesById[$id] = null;
                    break;
            }
        }

        // подготовка 2-го пакета запросов
        // запрашиваем товары
        if ((bool)$productsById) {
            \RepositoryManager::product()->prepareCollectionById(array_keys($productsById), $region, function($data) use (&$productsById) {
                foreach ($data as $item) {
                    $productsById[(int)$item['id']] = new \Model\Product\BasicEntity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить товары для промо-каталога');
            });
        }
        // запрашиваем услуги
        if ((bool)$servicesById) {
            \RepositoryManager::service()->prepareCollectionById(array_keys($servicesById), $region, function($data) use (&$servicesById) {
                foreach ($data as $item) {
                    $servicesById[(int)$item['id']] = new \Model\Product\Service\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить услуги для промо-каталога');
            });
        }
        // запрашиваем категории товаров
        if ((bool)$categoriesById) {
            \RepositoryManager::productCategory()->prepareCollectionById(array_keys($categoriesById), $region, function($data) use (&$categoriesById) {
                foreach ($data as $item) {
                    $categoriesById[(int)$item['id']] = new \Model\Product\Category\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить категории товаров для промо-каталога');
            });
        }

        if ((bool)$productsById || (bool)$servicesById || (bool)$categoriesById) {
            // выполнение 2-го пакета запросов
            $client->execute();
        }

        $repository = \RepositoryManager::promo();

        $slideData = [];
        foreach ($promo->getImage() as $image) {
            $repository->setEntityImageLink($image, $router, $productsById, $categoriesById, $servicesById);

            $slideData[] = [
                'imgUrl'  => \App::config()->dataStore['url'] . 'promo/' . $promo->getToken() . '/' . trim($image->getUrl(), '/'),
                'title'   => $image->getName(),
                'linkUrl' => $image->getLink(),
            ];
        }

        $page = new \View\Promo\IndexPage();
        $page->setParam('promo', $promo);
        $page->setParam('slideData', $slideData);

        return new \Http\Response($page->show());
    }
}