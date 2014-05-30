<?php

namespace Model\DeliveryType;

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
     * @return Entity[]
     */
    public function getCollection() {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $hasTransportCompany = \App::user()->getRegion()->getHasTransportCompany(); // Если регион ТК то показываем надпись "Доставка заказа транспортной компанией"

        $data = [
            [
                'id'                     => 1,
                'token'                  => 'standart',
                'short_name'             => 'доставка',
                'name'                   => $hasTransportCompany ? 'Доставка заказа транспортной компанией' : 'Доставка заказа курьером',
                'description'            => 'Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.',
                //'description'            => 'DHL, DPD, СПСР-Экспресс',
                'methods'                => ['standart_furniture', 'standart_other', 'standart', 'standart_svyaznoy'],
                'possible_method_tokens' => ['standart_furniture', 'standart_other', 'self', 'now', 'standart','standart_svyaznoy'],
            ],
            /*
            [
                'id'          => 2,
                'token'       => 'express',
            ],
            */
            [
                'id'                     => 3,
                'token'                  => 'self',
                'short_name'             => 'самовывоз',
                'name'                   => 'Самостоятельно заберу в магазине',
                'description'            => 'Вы можете самостоятельно забрать товар из ближайшего к вам магазина Enter. Услуга бесплатная! Резерв товара сохраняется 3 дня. Пожалуйста, выберите магазин.',
                'methods'                => ['self', 'self_svyaznoy'],
                'possible_method_tokens' => ['self', 'now', 'standart_furniture', 'standart_other', 'standart', 'self_svyaznoy'],
            ],
            [
                'id'                     => 4,
                'token'                  => 'now',
                'short_name'             => 'покупка в магазине',
                'name'                   => 'Заберу сейчас из магазина',
                'button_name'            => 'Забрать из этого магазина',
                'description'            => 'Вы можете забрать товар из магазина прямо сейчас',
                'methods'                => ['now'],
                'possible_method_tokens' => ['now', 'self', 'standart_furniture', 'standart_other', 'standart'],
            ],
            /*
            [
                'id'          => 5,
                'token'       => '',
            ],
            */
            [
                'id'                     => 6,
                'token'                  => 'pickpoint',
                'short_name'             => 'PickPoint',
                'name'                   => 'Самостоятельно забрать в постамате PickPoint',
                'button_name'            => 'Забрать из этого постамата',
                'description'            => 'Автоматический пункт выдачи заказов',
                'methods'                => ['pickpoint'],
                'possible_method_tokens' => ['pickpoint', 'now', 'self', 'standart_furniture', 'standart_other', 'standart'],
            ],
/*            [
                'id'                     => 7,
                'token'                  => 'standart_svyaznoy',
                'short_name'             => 'доставка',
                'name'                   => $hasTransportCompany ? 'Доставка заказа транспортной компанией' : 'Доставка заказа курьером',
                'description'            => 'Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.',
                //'description'            => 'DHL, DPD, СПСР-Экспресс',
                'methods'                => ['standart_furniture', 'standart_other', 'standart', 'standart_svyaznoy'],
                'possible_method_tokens' => ['standart_furniture', 'standart_other', 'self', 'now', 'standart', 'standart_svyaznoy'],
            ],*/
        ];

        $collection = [];
        foreach ($data as $item) {
            if (('now' === $item['token']) && !\App::config()->product['allowBuyOnlyInshop']) continue;
            $collection[] = new Entity($item);
        }

        return $collection;
    }

    /**
     * @param int $id
     * @return Entity|null
     */
    public function getEntityById($id) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if ($id == $entity->getId()) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @param int $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if ($token == $entity->getToken()) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @param int $methodToken
     * @return Entity|null
     */
    public function getEntityByMethodToken($methodToken) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if (in_array($methodToken, $entity->getMethodTokens())) {
                return $entity;
            }
        }

        return null;
    }


}