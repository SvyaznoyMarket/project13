<?php

namespace Controller\OrderV3OneClick;

class FormAction {
    /** Main function
     * @param \Http\Request $request
     * @param string $productUid
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request, $productUid) {
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
        $products = [new \Model\Product\Entity(['ui' => $productUid])];
        \RepositoryManager::product()->prepareProductQueries($products, 'media');

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$products) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productUid));
        }

        return new \Http\JsonResponse([
            'form' => \App::closureTemplating()->render('cart/__form-oneClick', [
                'product' => $products[0],
                'region'  => $user->getRegion(),
                'sender'  => (array)$request->get('sender') + ['name' => null, 'method' => null, 'position' => null],
                'sender2' => (string)$request->get('sender2'),
            ])
        ]);
    }
}