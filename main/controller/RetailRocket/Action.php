<?php

namespace Controller\RetailRocket;


/**
 * Пока что тестовый класс
 *
 * Class Action
 * @package Controller\RetailRocket
 */
class Action
{
    private $RetailRocket;


    public function __construct($config = [])
    {
        $this->RetailRocket = new \RetailRocket\Client( $config, \App::logger() );
    }


    public function execute($productId, \Http\Request $request)
    {

        $resp = $this->getRecomendation($request, $productId, 'UpSellItemToItems');

        print '<pre>rr';
        print_r($resp);
        print '</pre>';

    }


    public function getRecomendation( \Http\Request $request, $productId, $method = 'UpSellItemToItems' ) {
        \App::logger()->debug('Exec ' . __METHOD__);
        $RR = &$this->RetailRocket;

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);
            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $ids = $RR->query('Recomendation/' . $method, $productId);

            if (!count($ids)) throw new \Exception(sprintf('Не получено товаров методом %1$s для товара #%2$s', $method, $productId));

            $products = \RepositoryManager::product()->getCollectionById($ids);

            $return = [];
            foreach ($products as $i => $product) {
                if (!$product->getIsBuyable()) continue;

                $return[] = [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'image' => $product->getImageUrl(),
                    'rating' => $product->getRating(),
                    'link' => $product->getLink() . (false === strpos($product->getLink(), '?') ? '?' : '&') . 'sender=' . \RetailRocket\Client::NAME . '|' . $product->getId(),
                    'price' => $product->getPrice(),
                    'data' => \Kissmetrics\Manager::getProductEvent($product, $i + 1, 'Similar'),
                ];
            }

            if (!count($return)) throw new \Exception(sprintf('Нечего возвращать. Метод %1$s для товара #%2$s', $method, $productId));

            return new \Http\JsonResponse($return);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['RetailRocket']);
            return new \Http\JsonResponse();
        }

    }


}