<?php


namespace Model\Order;
use Partner\Counter\Actionpay;


/** Класс ля создания заказа на ядре
 * Class OrderEntity
 * @package Model\Order
 */
class OrderEntity {

    const TYPE_ORDER = 1;
    const DEFAULT_PAYMENT_ID = 1;
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
     * @param array $arr
     * @param array|null $sender
     * @param string $sender2
     * @throws \Exception
     */
    public function __construct($arr, $sender = null, $sender2 = '') {

        $request = \App::request();
        $region = \App::user()->getRegion();
        $regionName = $region ? $region->getName() : null;
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

        if (isset($arr['order']['certificate']['par']) && $arr['order']['certificate']['par'] !== null) {
            $this->certificate = $arr['order']['certificate']['code'];
            $this->certificate_pin = $arr['order']['certificate']['pin'];
            $this->payment_id = self::PAYMENT_ID_CERTIFICATE;
        }

        // идиотский АБ-тест TODO remove
        if (\Session\AbTest\AbTest::isSelfPaidDelivery() && $arr['total_cost'] < \App::config()->self_delivery['limit'] && $this->delivery_type_id == 3) {
            $this->delivery_price = 100;
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

        if (\App::config()->order['enableMetaTag']) $this->meta_data = $this->getMetaData($sender, $sender2);


    }

    /** Возвращает мета-данные для партнеров
     * @return array|null
     */
    private function getMetaData($sender, $sender2) {
        $request = \App::request();
        $user = \App::user();
        $data = [];
        $cart = $user->getCart()->getProductsNC();
        $oneClickCart = $user->getOneClickCart()->getProductSourceData();

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

                try {

                    // добавляем информацию о блоке рекомендаций, откуда был добавлен товар (используется корзина, которая очищается только на /order/complete)
                    if (isset($cart[$product->getId()]['sender'])) {
                        $senderData = $cart[$product->getId()]['sender'];
                        if (isset($senderData['name']))     $data[sprintf('product.%s.sender', $product->getUi())] = $senderData['name'];       // система рекомендаций
                        if (isset($senderData['position'])) $data[sprintf('product.%s.position', $product->getUi())] = $senderData['position']; // позиция блока на сайте
                        if (isset($senderData['method']))   $data[sprintf('product.%s.method', $product->getUi())] = $senderData['method'];     // метод рекомендаций
                        if (isset($senderData['from']) && !empty($senderData['from']))     $data[sprintf('product.%s.from', $product->getUi())] = $senderData['from'];         // откуда перешели на карточку товара
                        unset($senderData);
                    } else if ($sender) {
                        if (isset($sender['name']))     $data[sprintf('product.%s.sender', $product->getUi())] = $sender['name'];       // система рекомендаций
                        if (isset($sender['position'])) $data[sprintf('product.%s.position', $product->getUi())] = $sender['position']; // позиция блока на сайте
                        if (isset($sender['method']))   $data[sprintf('product.%s.method', $product->getUi())] = $sender['method'];     // метод рекомендаций
                        if (isset($sender['from']) && !empty($sender['from']))     $data[sprintf('product.%s.from', $product->getUi())] = $sender['from'];         // откуда перешели на карточку товара
                    } else if (isset($oneClickCart['product'][$product->getId()]['sender'])) {
                        $senderData = $oneClickCart['product'][$product->getId()]['sender'];
                        if (isset($senderData['name']))     $data[sprintf('product.%s.sender', $product->getUi())] = $senderData['name'];       // система рекомендаций
                        if (isset($senderData['position'])) $data[sprintf('product.%s.position', $product->getUi())] = $senderData['position']; // позиция блока на сайте
                        if (isset($senderData['method']))   $data[sprintf('product.%s.method', $product->getUi())] = $senderData['method'];     // метод рекомендаций
                        if (isset($senderData['from']) && !empty($senderData['from']))     $data[sprintf('product.%s.from', $product->getUi())] = $senderData['from'];         // откуда перешели на карточку товара
                        unset($senderData);
                    }

                    if (isset($cart[$product->getId()]['sender2']) && $cart[$product->getId()]['sender2']) {
                        $data[sprintf('product.%s.sender2', $product->getUi())] = $cart[$product->getId()]['sender2'];
                    } else if ($sender2) {
                        $data[sprintf('product.%s.sender2', $product->getUi())] = $sender2;
                    } else if (isset($oneClickCart['product'][$product->getId()]['sender2']) && $oneClickCart['product'][$product->getId()]['sender2']) {
                        $data[sprintf('product.%s.sender2', $product->getUi())] = $oneClickCart['product'][$product->getId()]['sender2'];
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
                if (\App::config()->partners['MnogoRu']['enabled'] && !empty($request->cookies->get(\App::config()->partners['MnogoRu']['cookieName']))) {
                    $data['mnogo_ru_card'] = $request->cookies->get(\App::config()->partners['MnogoRu']['cookieName']);
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
            if ($value !== null) $data[$key] = $value;
        }

        return $data;
    }

} 