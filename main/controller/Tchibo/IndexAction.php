<?php

namespace Controller\Tchibo;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $slideData = [];
        $repositoryPromo = \RepositoryManager::promo();
        $region = $user->getRegion();
        $promoToken = 'tchibo';

        $promo = $repositoryPromo->getEntityByToken($promoToken);


        // подготовка для 1-го пакета запросов в ядро
        // получим данные category
        \RepositoryManager::productCategory()->prepareEntityByToken($promoToken, $region, function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });

        // выполнение 1-го пакета запросов в ядро
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);


        if (!$promo) {
            throw new \Exception\NotFoundException(sprintf('Промо-каталог @%s не найден.', $promoToken));
        }
        /** @var $promo     \Model\Promo\Entity */

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена', $promoToken));
        }
        /** @var $category  \Model\Product\Category\Entity */



        // получаем catalog json для категории
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson($category);



        // подготовка для 2-го пакета запросов в ядро
        // получим данные для меню
        $rootCategoryIdInMenu = $category->getId();
        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($rootCategoryIdInMenu, $region, 3, function($data) use (&$rootCategoryInMenu) {
            $data = is_array($data) ? reset($data) : [];
            if (isset($data['id'])) {
                $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
            }
        });

        // выполнение 2-го пакета запросов в ядро
        $client->execute(\App::config()->coreV2['retryTimeout']['short']);



        // перевариваем данные изображений для слайдера в $slideData
        foreach ($promo->getImage() as $image) {
            $slideData[] = [
                'imgUrl'  => \App::config()->dataStore['url'] . 'promo/' . $promo->getToken() . '/' . trim($image->getUrl(), '/'),
                'title'   => $image->getName(),
                'linkUrl' => $image->getLink()?($image->getLink().'?from='.$promo->getToken()):'',
                // Пока не нужно, но в будущем, возможно понадобится делать $repositoryPromo->setEntityImageLink() как в /main/controller/Promo/IndexAction.php
            ];
        }


        // формируем вьюху, передаём ей данные
        $page = new \View\Tchibo\IndexPage();
        $page->setParam('slideData', $slideData);
        $page->setParam('rootCategoryInMenu', $rootCategoryInMenu);
        $page->setParam('catalogConfig', $catalogJson);

        return new \Http\Response($page->show());
    }
}