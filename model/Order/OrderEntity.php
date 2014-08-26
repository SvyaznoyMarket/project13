<?php


namespace Model\Order;


/** Класс ля создания заказа на ядре
 * Class OrderEntity
 * @package Model\Order
 */
class OrderEntity {

    const TYPE_ORDER = 1;
    const DEFAULT_PAYMENT_ID = 1;
    const PAYMENT_ID_CREDIT_CARD = 2;
    const PAYMENT_ID_CREDIT_ONLINE = 6;

    const DELIVERY_TYPE_ID_STANDART = 3;
    const DELIVERY_TYPE_ID_SELF = 154;

    /** Tип заказа
     * Обязательный
     * @var int
     */
    private $type_id;
    /** Регион
     * Обязательный
     * @var int
     */
    private $geo_id;
    /** Станция метро
     * @var int|null
     */
    private $subway_id;
    /** IP адрес
     * @var string|null
     */
    private $ip;
    /** Способ оплаты
     * Обязательный
     * @var int
     */
    private $payment_id;
    /** Статус оплаты
     * По умолчанию "Не оплачено"
     * @var int|null
     */
//    private $payment_status_id;
    /** Способ доставки
     * Обязательный
     * @var int
     */
    private $delivery_type_id;
    /** Токен доставки
     * Обязательный при партнерском заказе
     * @var string
     */
    private $delivery_type_token;
    /** Дата доставки (YYYY-MM-DD)
     * Обязательный (кроме случая, когда delivery_type_id=4 - забрать на месте)
     * @var string
     */
    private $delivery_date;
    /** Цена доставки
     * @var int
     */
    private $delivery_price;
    /** Интервал доставки
     * Необязательный (если указан delivery_period)
     * @var int
     */
    private $delivery_interval_id;
    /** Интервал доставки
     * Обязательный. В массиве первый элемент - время начала, второй время завершения интервала
     * @var array
     */
    private $delivery_period;
    /** Магазин для самовывоза
     * Обязательный в случае delivery_type_id=3 или 4
     * @var int|null
     */
    private $shop_id;
    /** ID авторизированного пользователя
     * @var int
     */
    private $user_id;
    /** Имя
     * @var string
     */
    private $first_name;
    /** Фамилия
     * @var string
     */
    private $last_name;
    /** Отчество
     * @var string
     */
    private $middle_name;
    /** Номер телефона
     * @var string
     */
    private $phone;
    /** Номер мобильного телефона
     * @var
     */
    private $mobile;
    /** Email
     * @var string
     */
    private $email;
    /** Идентификатор адреса из адресной книги
     * @var int
     */
//    private $address_id;
    /** Адрес доставки
     * Обязательный в случае самовывоза
     * @var string
     */
    private $address;
    /** Адрес доставки: улица
     * @var string
     */
    private $address_street;
    /** Адрес доставки: дом
     * @var string
     */
    private $address_building;
    /** Адрес доставки: подъезд
     * @var string
     */
//    private $address_number;
    /** Адрес доставки: этаж
     * @var string
     */
//    private $address_floor;
    /** Адрес доставки: квартира
     * @var string
     */
    private $address_apartment;
    /** Адрес доставки: почтовый индекс
     * @var string
     */
//    private $zip_code;
    /** Комментарии к заказу
     * @var string
     */
    private $extra;
    /** Отправлять ли sms оповещения
     * @var bool
     */
//    private $is_receive_sms;
    /** Номер бонусной карты лояльности
     * @var string
     */
    private $bonus_card_number;
    /** id банка, предоставляющего кредит
     * @var int
     */
//    private $credit_bank_id;
    /** Список товаров
     * Обязательно
     * @var array
     */
    private $product = [];
    /** Мета свойства
     * @var array
     */
    private $meta_data;
    /** Примененные скидки
     * @var array
     */
    private $action_list;

    /**
     * @param array $arr
     * @throws \Exception
     */
    public function __construct($arr) {

        $request = \App::request();
        $region = \App::user()->getRegion();
        $regionName = $region ? $region->getName() : null;
        $user = \App::user()->getEntity();

        /*
         * ОБЯЗАТЕЛЬНЫЕ СВОЙСТВА
         */

        $this->type_id = self::TYPE_ORDER;

        $this->geo_id = \App::user()->getRegionId();
        if ($this->geo_id === null) throw new \Exception('Невозможно определить регион пользователя');

        if (isset($arr['order']['payment_method_id']) && $arr['order']['payment_method_id'] !== null) {
            $this->payment_id = (int)$arr['order']['payment_method_id'];
        } else {
            $this->payment_id = self::DEFAULT_PAYMENT_ID;
        }

        if (isset($arr['order']['delivery']['mode_id'])) {
            if ($arr['order']['delivery']['mode_id'] === null) $this->delivery_type_id = null;
            else $this->delivery_type_id = (int)$arr['order']['delivery']['mode_id'];
        } else {
            throw new \Exception('Не указан mode_id');
        }

        /* TODO potentially weak check */
        if (isset($arr['order']['delivery']['date']) && $arr['order']['delivery']['date'] !== null) {
            $this->delivery_date = date('Y-m-d', (int)$arr['order']['delivery']['date']);
        } else {
            if ($this->delivery_type_id !== 4) throw new \Exception('Не указана дата доставки');
        }

        if (isset($arr['order']['delivery']['interval']['from']) && isset($arr['order']['delivery']['interval']['to'])) {
            $this->delivery_period = $arr['order']['delivery']['interval'];
        } else {
            //throw new \Exception('Не указан интервал доставки');
        }

        if (isset($arr['order']['delivery']['point']['id'])) {
            $this->shop_id = (int)$arr['order']['delivery']['point']['id'];
        } else {
            if ($this->delivery_type_id === self::DELIVERY_TYPE_ID_SELF) throw new \Exception('Не указан магазин для самовывоза');
        }

        if (isset($arr['order']['products']) && is_array($arr['order']['products']) && count($arr['order']['products']) > 0) {
            foreach ($arr['order']['products'] as $product) {
                $this->product[] = ['id' => (int)$product['id'], 'quantity' => (int)$product['quantity']];
            }
        } else {
            throw new \Exception('Нет продуктов для заказа');
        }

        /*
         * НЕОБЯЗАТЕЛЬНЫЕ СВОЙСТВА
         */

        $this->ip = $request->getClientIp();

        if (isset($arr['order']['delivery']['delivery_method_token']) && !empty($arr['order']['delivery']['delivery_method_token'])) $this->delivery_type_token = (string)$arr['order']['delivery']['delivery_method_token'];

        if (isset($arr['order']['delivery']['price'])) $this->delivery_price = (int)$arr['order']['delivery']['price'];

        // идиотский АБ-тест TODO remove
        if (\App::user()->getRegionId() == 93746 && $this->delivery_type_id == 3 && $arr['total_cost'] < 1000 && \App::abTest()->getTest('order_delivery_price')  && \App::abTest()->getTest('order_delivery_price')->getChosenCase()->getKey() == 'delivery_self_100') {
            $this->delivery_price = 100;
        }

        if ($user) {
            $this->user_id = $user->getId();
            if ($user->getMiddleName() !== null && $user->getMiddleName() !== '') $this->middle_name = $user->getMiddleName();
            if ($user->getHomePhone() !== null && $user->getHomePhone() !== '') $this->phone = $user->getHomePhone();
            if ($user->getLastName() !== null && $user->getLastName() !== '') $this->last_name = $user->getLastName();
        }

        if (isset($arr['user_info']['first_name']) && $arr['user_info']['first_name'] !== '') $this->first_name = (string)$arr['user_info']['first_name'];

        if (isset($arr['user_info']['phone']) && $arr['user_info']['phone'] !== '') $this->mobile = (string)$arr['user_info']['phone'];
        if (isset($arr['user_info']['email']) && $arr['user_info']['email'] !== '') $this->email = (string)$arr['user_info']['email'];

        if (isset($arr['user_info']['address']['street']) && $arr['user_info']['address']['street'] !== '') $this->address_street = (string)$arr['user_info']['address']['street'];
        if (isset($arr['user_info']['address']['building']) && $arr['user_info']['address']['building'] !== '') $this->address_building = (string)$arr['user_info']['address']['building'];
        if (isset($arr['user_info']['address']['apartment']) && $arr['user_info']['address']['apartment'] !== '') $this->address_apartment = (string)$arr['user_info']['address']['apartment'];
        if (isset($arr['user_info']['bonus_card']) && $arr['user_info']['bonus_card'] !== '') $this->bonus_card_number = (string)$arr['user_info']['bonus_card'];

        $this->address = sprintf('%s, %s, д. %s, кв. %s', $regionName, $this->address_street, $this->address_building, $this->address_apartment);

        if (isset($arr['order']['comment']) && $arr['order']['comment'] !== '') $this->extra = (string)$arr['order']['comment'];

        if (isset($arr['order']['action_list']) && is_array($arr['order']['action_list']) && (bool)$arr['order']['action_list']) $this->action_list = $arr['order']['action_list'];

        if (\App::config()->order['enableMetaTag']) $this->meta_data = $this->getMetaData();


    }

    /** Возвращает мета-данные для партнеров
     * @return array|null
     */
    private function getMetaData() {
        $request = \App::request();
        $user = \App::user();
        $data = [];
        try {
            /** @var $products \Model\Product\Entity[] */
            $products = [];
            \RepositoryManager::product()->prepareCollectionById(array_map(function($product){return $product['id']; }, $this->product), $user->getRegion(), function($data) use(&$products) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\Entity($item);
                }
            }, function(\Exception $e) { \App::exception()->remove($e); });
            \App::coreClientV2()->execute();

            foreach ($products as $product) {
                $partners = [];
                if ($partnerName = \App::partner()->getName()) {
                    $partners[] = \App::partner()->getName();
                }
                foreach (\Controller\Product\BasicRecommendedAction::$recomendedPartners as $recomPartnerName) {
                    if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at')) {
                        if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { // 30days
                            $partners[] = $recomPartnerName;
                        } else {
                            \App::user()->deleteRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at');
                        }
                    }
                }
                $data['meta_data'] = \App::partner()->fabricateCompleteMeta(
                    isset($data['meta_data']) ? $data['meta_data'] : [],
                    \App::partner()->fabricateMetaByPartners($partners, $product)
                );
                $data['meta_data']['user_agent'] = $request->server->get('HTTP_USER_AGENT');
                $data['meta_data']['kiss_session'] = $request->request->get('kiss_session');
                $data['meta_data']['last_partner'] = $request->cookies->get('last_partner');
            }
            \App::logger()->info(sprintf('Создается заказ от партнеров %s', json_encode($data['meta_data']['partner'])), ['order', 'partner']);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['order_v3', 'partner']);
        }
        return (bool)$data ? $data : null;
    }

    /** Возвращает данные для создания заказа на ядре
     * @return array
     */
    public function getOrderData() {
        $data = [];

        // создаем заказ с оплатой наличными, если выбран кредит, а предпочтительный метод записываем в meta
        $this->meta_data['preferred_payment_id'] = $this->payment_id;
        if ($this->payment_id == self::PAYMENT_ID_CREDIT_ONLINE) $this->payment_id = self::DEFAULT_PAYMENT_ID;

        // добавляем в мета-данные параметр о новом заказе
        $this->meta_data['split_version'] = 2;

        foreach (get_object_vars($this) as $key => $value) {
            if ($value !== null) $data[$key] = $value;
        }

        return $data;
    }

} 