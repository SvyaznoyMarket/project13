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

        /** @var \Model\Product\Entity|null $product */
        $product = null;

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

        \RepositoryManager::product()->prepareEntityByUid($productUid, function($data) use (&$product) {
            if (!is_array($data)) return;

            if ($data = reset($data)) {
                if (is_array($data)) {
                    $product = new \Model\Product\Entity($data);
                }
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

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productUid));
        }

        return new \Http\JsonResponse([
            'form' => \App::closureTemplating()->render('cart/__form-oneClick', [
                'product' => $product,
                'region'  => $user->getRegion(),
                'sender'  => (array)$request->get('sender') + ['name' => null, 'method' => null, 'position' => null],
                'sender2' => (string)$request->get('sender2'),
            ])
        ]);
    }
}