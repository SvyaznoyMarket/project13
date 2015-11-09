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
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $hasTransportCompany = \App::user()->getRegion()->getHasTransportCompany(); // Если регион ТК то показываем надпись "Доставка заказа транспортной компанией"

        $data = [
            [
                'id'                     => 1,
                'token'                  => 'standart',
                'short_name'             => 'Доставка',
                'name'                   => $hasTransportCompany ? 'Доставка заказа транспортной компанией' : 'Доставка заказа курьером',
                'description'            => 'Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.',
                //'description'            => 'DHL, DPD, СПСР-Экспресс',
                'methods'                => ['standart_furniture', 'standart_other', 'standart', 'standart_svyaznoy', 'standart_pred_supplier', 'standart_bu', 'standart_fortochki'],
                'possible_method_tokens' => ['standart_furniture', 'standart_other', 'self', 'now', 'standart','standart_svyaznoy', 'standart_pred_supplier', 'self_pred_supplier', 'standart_bu', 'standart_fortochki'],
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
                'short_name'             => 'Самовывоз',
                'name'                   => 'Самостоятельно заберу в магазине',
                'description'            => 'Вы можете самостоятельно забрать товар из ближайшего к вам магазина. Услуга бесплатная! Резерв товара сохраняется 3 дня. Пожалуйста, выберите магазин.',
                'methods'                => ['self', 'self_svyaznoy', 'self_pred_supplier'],
                'possible_method_tokens' => ['self', 'now', 'self_pred_supplier', 'standart_furniture', 'standart_other', 'standart', 'self_svyaznoy', 'standart_pred_supplier'],
            ],
            [
                'id'                     => 4,
                'token'                  => 'now',
                'short_name'             => 'Самовывоз',
                'name'                   => 'Заберу сейчас из магазина',
                'button_name'            => 'Забрать из этого магазина',
                'description'            => 'Вы можете забрать товар из магазина прямо сейчас',
                'methods'                => ['now'],
                'possible_method_tokens' => ['now', 'self', 'self_pred_supplier', 'standart_furniture', 'standart_other', 'standart', 'standart_pred_supplier'],
            ],
            /*
            [
                'id'          => 5,
                'token'       => '',
            ],
            */
            [
                'id'                     => 6,
                'token'                  => 'self_partner_pickpoint',
                'short_name'             => 'PickPoint',
                'name'                   => 'Самостоятельно забрать в постамате PickPoint',
                'button_name'            => 'Забрать из этого постамата',
                'description'            => 'Автоматический пункт выдачи заказов',
                'methods'                => ['self_partner_pickpoint'],
                'possible_method_tokens' => ['self_partner_pickpoint', 'now', 'self', 'self_pred_supplier', 'standart_furniture', 'standart_other', 'standart', 'standart_pred_supplier'],
            ],
            [
                'id'                     => 8,
                'token'                  => 'self_partner_euroset',
                'short_name'             => 'Евросеть',
                'name'                   => 'Самостоятельно забрать в магазине Евросети',
                'button_name'            => 'Забрать из этого магазина',
                'description'            => '',
                'methods'                => ['self_partner_euroset'],
                'possible_method_tokens' => ['self_partner_euroset', 'self_partner_pickpoint', 'now', 'self', 'self_pred_supplier', 'standart_furniture', 'standart_other'],
            ],
            [
                'id'                     => 9,
                'token'                  => 'self_partner_hermes',
                'short_name'             => 'Hermes DPD',
                'name'                   => 'Самостоятельно забрать в Hermes',
                'button_name'            => 'Забрать из этого пункта выдачи',
                'description'            => '',
                'methods'                => ['self_partner_hermes'],
                'possible_method_tokens' => ['self_partner_hermes', 'self_partner_euroset', 'self_partner_pickpoint', 'now', 'self', 'self_pred_supplier', 'standart_furniture', 'standart_other'],
            ],
            /*[
                'id'                     => 7,
                'token'                  => 'standart_svyaznoy',
                'short_name'             => 'доставка',
                'name'                   => $hasTransportCompany ? 'Доставка заказа транспортной компанией' : 'Доставка заказа курьером',
                'description'            => 'Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.',
                //'description'            => 'DHL, DPD, СПСР-Экспресс',
                'methods'                => ['standart_furniture', 'standart_other', 'standart', 'standart_svyaznoy'],
                'possible_method_tokens' => ['standart_furniture', 'standart_other', 'self', 'now', 'standart', 'standart_svyaznoy', 'standart_pred_supplier', 'self_pred_supplier'],
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
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        foreach ($this->getCollection() as $entity) {
            if (in_array($methodToken, $entity->getMethodTokens())) {
                return $entity;
            }
        }

        return null;
    }


}
