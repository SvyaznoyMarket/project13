<?php

namespace Controller\Product;

class UpsellAction {

    public function execute($productToken, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            // выполнение 1-го пакета запросов
            $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);
        }

        $region = $user->getRegion();

        $product = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            $data = reset($data);
            if ((bool)$data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        $page = new \View\Product\UpsellPage();
        $page->setParam('product', $product);

        return new \Http\Response($page->show());
    }

}