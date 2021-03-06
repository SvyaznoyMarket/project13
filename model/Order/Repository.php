<?php

namespace Model\Order;

/**
 * Class Repository
 * @package Model\Order
 * @link https://wiki.enter.ru/pages/viewpage.action?pageId=5374028 order/get order/get-limited
 * @link https://wiki.enter.ru/pages/viewpage.action?pageId=6390064 order/get-by-mobile
 */
class Repository {
    /** @var \Core\ClientInterface */
    private $client;

    /**
     * @param \Core\ClientInterface $client
     */
    public function __construct(\Core\ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @param string $userToken
     * @return int Количество заказов
     */
    public function countByUserToken($userToken) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $count = null;
        $client->addQuery('order/get', ['token' => $userToken], [], function ($data) use (&$count) {
            $count = (bool)$data ? count($data) : 0;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $count;
    }

    /**
     * @param string $userToken
     * @return Entity[]
     */
    public function getCollectionByUserToken($userToken) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $collection = [];
        $client->addQuery('order/get', ['token' => $userToken], [], function ($data) use (&$collection) {
            foreach ($data as $item) {
                $collection[] = new Entity($item);
            }
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $collection;
    }

    /**
     * @param string $userToken
     * @param $callback
     * @param $offset
     * @param $limit
     * @return void
     */
    public function prepareCollectionByUserToken($userToken, $callback, $offset = 0, $limit = 20) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('order/get-limited', ['token' => $userToken, 'offset' => $offset, 'limit' => $limit], [], $callback, null, \App::config()->coreV2['hugeTimeout']);
    }

    /**
     * @deprecated
     *
     * @param string $userToken
     * @param int $id
     * @return mixed|null
     */
    public function getOrderById($userToken, $id) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $order = null;

        $client->addQuery('order/get', ['token' => $userToken, 'id' => $id], [], function ($data) use (&$order) {
            if (isset($data[0])) $order = $data[0];
        });

        $client->execute();

        return $order;
    }

    /**
     * @param string $number
     * @param string $phone
     * @return Entity|null
     */
    public function getEntityByNumberAndPhone($number, $phone) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery('order/get-by-mobile', ['number' => $number, 'mobile' => $phone], [], function ($data) use (&$entity) {
            $data = reset($data);
            $entity = $data ? new Entity($data) : null;
        });

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * @param string $accessToken
     * @return Entity|null
     */
    public function getEntityByAccessToken($accessToken) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $client = clone $this->client;

        $entity = null;
        $client->addQuery(
            'order/get-by-token',
            ['token' => $accessToken],
            [],
            function ($data) use (&$entity) {
                if ($data) {
                    $data = reset($data);
                    $entity = $data ? new Entity($data) : null;
                }
            },
            null,
            3 * \App::config()->coreV2['timeout']
        );

        $client->execute(\App::config()->coreV2['retryTimeout']['default']);

        return $entity;
    }

    /**
     * Подготавливает запрос для получения medias товаров заказов и, после выполнения запроса, задаёт medias для товаров
     * @param \Model\OrderDelivery\Entity $orderDelivery
     */
    public function prepareOrderDeliveryProducts(\Model\OrderDelivery\Entity $orderDelivery) {
        $productIds = [];
        foreach ($orderDelivery->orders as $order) {
            foreach ($order->products as $product) {
                $productIds[] = $product->id;
            }
        }

        if (!$productIds) {
            return;
        }

        \App::scmsClient()->addQuery(
            'product/get-description/v1',
            [
                'ids' => $productIds,
                'media' => 1,
                'label' => 1,
            ],
            [],
            function($data) use($orderDelivery) {
                if (!isset($data['products']) || !is_array($data['products'])) {
                    return;
                }

                foreach ($data['products'] as $product) {
                    if (!isset($product['core_id']) || !isset($product['medias'])) {
                        continue;
                    }

                    $medias = array_map(function($media) { return new \Model\Media($media); }, $product['medias']);

                    // Учитываем ситуацию, когда товар с одним и тем же id присутствует в разных заказах (в таком
                    // случае объекты товаров будут разные)
                    foreach($orderDelivery->orders as $order) {
                        foreach ($order->products as $orderProduct) {
                            if (isset($product['core_id']) && $orderProduct->id == $product['core_id']) {
                                $orderProduct->link = isset($product['url']) ? (string)$product['url'] : '';
                                $orderProduct->name = isset($product['name']) ? (string)$product['name'] : '';
                                $orderProduct->name_web = isset($product['name_web']) ? (string)$product['name_web'] : '';
                                $orderProduct->prefix = isset($product['name_prefix']) ? (string)$product['name_prefix'] : '';
                                $orderProduct->medias = $medias;
                            }
                        }
                    }
                }
            }
        );
    }
}