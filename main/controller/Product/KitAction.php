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

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['ui' => $productUi])];
        \RepositoryManager::product()->prepareProductQueries($products, 'media');

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$products) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productUi));
        }

        if (!$products[0]->getKit()) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не является набором.', $productUi));
        }

        $kitProducts = \RepositoryManager::product()->getKitProducts($products[0]);

        return new \Http\JsonResponse([
            'product' => [
                'id' => $products[0]->id,
                'ui' => $products[0]->ui,
                'article' => $products[0]->getArticle(),
                'prefix' => $products[0]->getPrefix(),
                'webname' => $products[0]->getWebname(),
                'imageUrl' => $products[0]->getMainImageUrl('product_500'),
                'kitProducts' => \App::config()->lite['enabled'] ? array_values($kitProducts) : $kitProducts,
            ],
            'template' => file_exists( $templatePath = \App::config()->appDir . '/lite/template/product/blocks/kit.mustache' )
                ? file_get_contents( $templatePath )
                : ''
        ]);
    }
}