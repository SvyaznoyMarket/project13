<?php

namespace Controller\Product;

class KitAction {
    /** Main function
     * @param \Http\Request $request
     * @param string $productUi
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request, $productUi) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        /** @var \Model\Product\Entity|null $product */
        $product = null;
        $medias = [];

        // запрашиваем текущий регион, если есть кука региона
        $regionConfig = [];
        if ($user->getRegionId()) {
            $regionConfig = (array)\App::dataStoreClient()->query("/region/{$user->getRegionId()}.json");

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        \RepositoryManager::product()->prepareEntityByUid($productUi, function($data) use (&$product) {
            if (!is_array($data)) return;

            if ($data = reset($data)) {
                if (is_array($data)) {
                    $product = new \Model\Product\Entity($data);
                }
            }
        });

        \RepositoryManager::product()->prepareProductsMediasByUids([$productUi], $medias);

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if ($product) {
            \RepositoryManager::product()->setMediasForProducts([$product->getId() => $product], $medias);
        }

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            $user->setRegion($regionEntity);
        }

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productUi));
        }

        if (!$product->getKit()) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не является набором.', $productUi));
        }

        return new \Http\JsonResponse([
            'product' => [
                'id' => $product->getId(),
                'article' => $product->getArticle(),
                'prefix' => $product->getPrefix(),
                'webname' => $product->getWebname(),
                'imageUrl' => $product->getMainImageUrl('product_500'),
                'kitProducts' => \RepositoryManager::product()->getKitProducts($product),
            ],
        ]);
    }
}