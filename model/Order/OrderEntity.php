<?php


namespace Model\Order;
use Partner\Counter\Actionpay;


/** Класс для создания заказа на ядре
 * Class OrderEntity
 * @package Model\Order
 */
class OrderEntity {

    const TYPE_ORDER = 1;
    const DEFAULT_PAYMENT_ID = 1;
    const DEFAULT_PAYMENT_UI = 'ff291b2a-ef90-11e0-83b5-005056af2ef1';
    const PAYMENT_ID_CREDIT_CARD = 2;
    const PAYMENT_ID_CREDIT_ONLINE = 6;
    const PAYMENT_ID_CERTIFICATE = 10;

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
//    private $subway_id;
    /** IP адрес
     * @var string|null
     */
    private $ip;
    /** Способ оплаты
     * Обязательный
     * @var int
     */
    private $payment_id;
    /** Способ оплаты
     * Обязательный
     * @var int
     */
    private $payment_ui;
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
//    private $delivery_interval_id;
    /**
     * Интервалы дат доставки:
     * Пример: { from: 2015-09-24, to: 2015-10-03 }
     *
     * @var array|null
     */
    private $delivery_date_interval;
    /** Интервал доставки
     * Обязательный. В массиве первый элемент - время начала, второй время завершения интервала
     * @var array
     */
    private $delivery_period;
    /** Магазин для самовывоза
     * Обязательный в случае delivery_type_id=3 или 4
     * @var string|null
     */
    private $shop_id;
    /** Точка самовывоза
     * Обязательный параметр в случае самовывоза
     * @var string|null
     */
    private $point_ui;
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
    private $action;
    /** КЛАДР - идентификатор адреса
     * @var string
     */
    private $kladr_id;
    /** Номер сертификата
     * @var string
     */
    private $certificate;
    /** ПИН сертификата
     * Обязательный, если есть $certificate
     * @var string
     */
    private $certificate_pin;
    /**
     * Ui коробки пикпоинта
     *
     * @var string|null
     */
    private $box_ui;
    /**
     * Цена заказа при онлайн-оплате
     * @var float
     */
    private $total_view_cost;
    /**
     * Сумма предоплаты
     * @var int
     */
    private $prepaid_sum;

    /** TODO принимать \Model\OrderDelivery\Entity\Order и \Model\OrderDelivery\Entity\UserInfo
     * @param array $arr
     * @param array|null $sender
     * @param string $sender2
     * @param \Model\Cart\Product\Entity[] $cartProducts
     * @throws \Exception
     */
    public function __construct($arr, $sender = null, $sender2 = '', $cartProducts = []) {

        $request = \App::request();
        $user = \App::user()->getEntity();

        /*
         * ОБЯЗАТЕЛЬНЫЕ СВОЙСТВА
         */

        $this->type_id = self::TYPE_ORDER;

        $this->geo_id = \App::user()->getRegion()->getId();
        if ($this->geo_id === null) throw new \Exception('Невозможно определить регион пользователя');

        if (isset($arr['order']['payment_method_id']) && $arr['order']['payment_method_id'] !== null) {
            $this->payment_id = (int)$arr['order']['payment_method_id'];
        } else {
            $this->payment_id = self::DEFAULT_PAYMENT_ID;
        }
        if (isset($arr['order']['payment_method_ui']) && $arr['order']['payment_method_ui'] !== null) {
            $this->payment_ui = (int)$arr['order']['payment_method_ui'];
        } else {
            $this->payment_ui = self::DEFAULT_PAYMENT_UI;
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

        if (isset($arr['order']['delivery']['date_interval']['from']) && isset($arr['order']['delivery']['date_interval']['to'])) {
            $this->delivery_date_interval = $arr['order']['delivery']['date_interval'];
        } else {
            $this->delivery_date_interval = null;
        }

        if (isset($arr['order']['delivery']['point']['id'])) {
            $this->shop_id = $arr['order']['delivery']['point']['id'];
        } else {
            if ($this->delivery_type_id === self::DELIVERY_TYPE_ID_SELF) throw new \Exception('Не указан магазин для самовывоза');
        }

        if (isset($arr['order']['delivery']['point']['ui'])) {
            $this->point_ui = $arr['order']['delivery']['point']['ui'];
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

        if (isset($arr['order']['certificate']['par']) && $arr['order']['certificate']['par'] !== null) {
            $this->certificate = $arr['order']['certificate']['code'];
            $this->certificate_pin = $arr['order']['certificate']['pin'];
            $this->payment_id = self::PAYMENT_ID_CERTIFICATE;
        }

        if ($user) {
            $this->user_id = $user->getId();
            if ($user->getMiddleName() !== null && $user->getMiddleName() !== '') $this->middle_name = $user->getMiddleName();
            if ($user->getHomePhone() !== null && $user->getHomePhone() !== '') $this->phone = $user->getHomePhone();
            if ($user->getLastName() !== null && $user->getLastName() !== '') $this->last_name = $user->getLastName();
        }

        if (isset($arr['user_info']['first_name']) && $arr['user_info']['first_name'] !== '') $this->first_name = (string)$arr['user_info']['first_name'];

        if (isset($arr['user_info']['phone']) && $arr['user_info']['phone'] !== '') $this->mobile = preg_replace('/\s+/','',(string)$arr['user_info']['phone']);
        if (isset($arr['user_info']['email']) && $arr['user_info']['email'] !== '') $this->email = (string)$arr['user_info']['email'];

        if (isset($arr['user_info']['address']['street']) && $arr['user_info']['address']['street'] !== '') $this->address_street = (string)$arr['user_info']['address']['street'];
        if (isset($arr['user_info']['address']['building']) && $arr['user_info']['address']['building'] !== '') $this->address_building = (string)$arr['user_info']['address']['building'];
        if (isset($arr['user_info']['address']['apartment']) && $arr['user_info']['address']['apartment'] !== '') $this->address_apartment = (string)$arr['user_info']['address']['apartment'];
        if (isset($arr['user_info']['address']['kladr_id']) && $arr['user_info']['address']['kladr_id'] !== '') $this->kladr_id = (string)$arr['user_info']['address']['kladr_id'];
        if (isset($arr['user_info']['bonus_card_number']) && $arr['user_info']['bonus_card_number'] !== '') $this->bonus_card_number = preg_replace('/\s+/','',(string)$arr['user_info']['bonus_card_number']);

        if ($this->shop_id === null && !empty($this->address_street)) {
            $this->address = $this->address_street;
            if (!empty($this->address_building)) $this->address .= ', д. '.$this->address_building;
            if (!empty($this->address_apartment)) $this->address .= ', кв. '.$this->address_apartment;
        }

        if (isset($arr['order']['comment']) && $arr['order']['comment'] !== '') $this->extra = (string)$arr['order']['comment'];

        if (isset($arr['order']['actions']) && is_array($arr['order']['actions']) && (bool)$arr['order']['actions']) $this->action = $arr['order']['actions'];

        if (isset($arr['order']['delivery']['box_ui'])) $this->box_ui = $arr['order']['delivery']['box_ui'];

        if (isset($arr['order']['total_view_cost'])) $this->total_view_cost = $arr['order']['total_view_cost'];

        // meta data
        if (\App::config()->order['enableMetaTag']) $this->meta_data = $this->getMetaData($sender, $sender2, $cartProducts);

        if (!empty($arr['order']['prepaid_sum'])) { // SITE-6256
            $this->meta_data['prepaid_sum'] = $arr['order']['prepaid_sum'];
        }
    }

    /** Возвращает мета-данные для партнеров
     * @param \Model\Cart\Product\Entity[] $cartProducts
     * @return array|null
     */
    private function getMetaData($sender, $sender2, $cartProducts) {
        $request = \App::request();
        $data = [];

        try {
            /** @var $products \Model\Product\Entity[] */
            $products = array_map(function($product){
                return new \Model\Product\Entity(['id' => $product['id']]);
            }, $this->product);
            
            \RepositoryManager::product()->prepareProductQueries($products, 'category');
            \App::coreClientV2()->execute();

            foreach ($products as $product) {
                $partners = [];
                if ($partnerName = \App::partner()->getName()) {
                    $partners[] = \App::partner()->getName();
                }

                try {

                    // добавляем информацию о блоке рекомендаций, откуда был добавлен товар (используется корзина, которая очищается только на /order/complete)
                    if ($sender) {
                        if (isset($sender['name']))     $data[sprintf('product.%s.sender', $product->getUi())] = $sender['name'];       // система рекомендаций
                        if (isset($sender['position'])) $data[sprintf('product.%s.position', $product->getUi())] = $sender['position']; // позиция блока на сайте
                        if (isset($sender['method']))   $data[sprintf('product.%s.method', $product->getUi())] = $sender['method'];     // метод рекомендаций
                        if (!empty($sender['from']))    $data[sprintf('product.%s.from', $product->getUi())] = $sender['from'];         // откуда перешели на карточку товара
                        if (!empty($sender['isFromProductCard']))    $data[sprintf('product.%s.isFromProductCard', $product->getUi())] = $sender['isFromProductCard']; // SITE-5772
                    } else if (isset($cartProducts[$product->getId()]) && $cartProducts[$product->getId()]->sender) {
                        $cartProductSender = $cartProducts[$product->getId()]->sender;
                        if (isset($cartProductSender['name']))     $data[sprintf('product.%s.sender', $product->getUi())] = $cartProductSender['name'];       // система рекомендаций
                        if (isset($cartProductSender['position'])) $data[sprintf('product.%s.position', $product->getUi())] = $cartProductSender['position']; // позиция блока на сайте
                        if (isset($cartProductSender['method']))   $data[sprintf('product.%s.method', $product->getUi())] = $cartProductSender['method'];     // метод рекомендаций
                        if (!empty($cartProductSender['from']))    $data[sprintf('product.%s.from', $product->getUi())] = $cartProductSender['from'];         // откуда перешели на карточку товара
                        if (!empty($cartProductSender['isFromProductCard']))    $data[sprintf('product.%s.isFromProductCard', $product->getUi())] = $cartProductSender['isFromProductCard']; // SITE-5772
                        unset($cartProductSender);
                    }

                    if ($sender2) {
                        $data[sprintf('product.%s.sender2', $product->getUi())] = $sender2;
                    } else if (isset($cartProducts[$product->getId()]) && $cartProducts[$product->getId()]->sender2) {
                        $data[sprintf('product.%s.sender2', $product->getUi())] = $cartProducts[$product->getId()]->sender2;
                    }
                } catch (\Exception $e) {
                    \App::logger()->error(['error' => $e], ['order', 'partner']);
                }

                $data = \App::partner()->fabricateCompleteMeta(
                    isset($data) ? $data : [],
                    \App::partner()->fabricateMetaByPartners($partners, $product)
                );
                $data['user_agent'] = $request->server->get('HTTP_USER_AGENT');
                $data['last_partner'] = $request->cookies->get('last_partner');

                // Много.ру
                if (\App::config()->partners['MnogoRu']['enabled']) {
                    $mnogoruCookieValue = $request->cookies->get(\App::config()->partners['MnogoRu']['cookieName']);
                    if (!empty($mnogoruCookieValue) && $mnogoruCookieValue != 'undefined') {
                        $data['mnogo_ru_card'] = $mnogoruCookieValue;
                    }
                }


                // Присваиваем заказ actionpay, если активировали промокод через PandaPay
                if (!empty($request->cookies->get(\App::config()->partners['PandaPay']['cookieName']))) {
                    $data['last_partner'] = Actionpay::NAME;
                }
            }
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
//        $this->meta_data['preferred_payment_id'] = $this->payment_id;
//        if ($this->payment_id == self::PAYMENT_ID_CREDIT_ONLINE) $this->payment_id = self::DEFAULT_PAYMENT_ID;

        // добавляем в мета-данные параметр о новом заказе
        $this->meta_data['split_version'] = 2;

        foreach (get_object_vars($this) as $key => $value) {
            if (
                (null !== $value)
                || ('delivery_date_interval' === $key)
            ) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

} 