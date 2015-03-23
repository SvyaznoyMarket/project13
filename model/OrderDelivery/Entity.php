<?php

namespace Model\OrderDelivery {

    use Model\OrderDelivery\Entity\ValidationError;

    class Entity {
        /** Глобальные группы доставки
         * @var Entity\DeliveryGroup[]
         */
        public $delivery_groups = [];
        /** Методы доставки
         * @var Entity\DeliveryMethod[]
         */
        public $delivery_methods = [];
        /** Точки самовывоза
         * @var Entity\Point[]
         */
        public $points = [];
        /** Массив заказов
         * @var Entity\Order[]
         */
        public $orders = [];
        /** Методы оплаты
         * @var Entity\PaymentMethod[]
         */
        public $payment_methods = [];
        /** Информация о клиенте
         * @var Entity\UserInfo|null
         */
        public $user_info;
        /** Общая стоимость заказов
         * @var float
         */
        public $total_cost;
        /** Ошибки разбиения
         * @var Error[]
         */
        public $errors = [];

        public function __construct(array $data = []) {

            if (isset($data['delivery_groups']) && is_array($data['delivery_groups'])) {
                foreach ($data['delivery_groups'] as $item) {
                    if (!isset($item['id'])) continue;

                    $this->delivery_groups[(string)$item['id']] = new Entity\DeliveryGroup($item);
                }
            } else {
                throw new \Exception('Отстуствуют данные по группам доставки');
            }

            if (isset($data['delivery_methods']) && is_array($data['delivery_methods'])) {
                foreach ($data['delivery_methods'] as $item) {
                    if (!isset($item['token'])) continue;

                    $this->delivery_methods[(string)$item['token']] = new Entity\DeliveryMethod($item);
                }
            } else {
                throw new \Exception('Отстуствуют данные методам доставки');
            }

            if (isset($data['points']) && is_array($data['points'])) {
                foreach ($data['points'] as $itemToken => $item) {
                    $item['token'] = $itemToken;

                    $this->points[$item['token']] = new Entity\Point($item);
                }
            }

            if (isset($data['payment_methods']) && is_array($data['payment_methods'])) {
                foreach ($data['payment_methods'] as $item) {
                    $this->payment_methods[$item['id']] = new Entity\PaymentMethod($item);
                }
            } else {
                throw new \Exception('Отстуствуют данные по методам оплаты');
            }

            if (isset($data['orders']) && is_array($data['orders']) && (bool)$data['orders']) {
                foreach ($data['orders'] as $key => $item) {
                    $this->orders[$key] = new Entity\Order($item, $this);
                }
            } else {
                //throw new \Exception('Отстуствуют данные по заказам');
            }

            if (isset($data['user_info'])) $this->user_info = new Entity\UserInfo($data['user_info']);

            if (isset($data['total_cost'])) {
                $this->total_cost = (float)$data['total_cost'];
            } else {
                throw new \Exception('Отстуствует общая стоимость заказа');
            }

            if (isset($data['errors']) && is_array($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    $this->errors[] = new Error($error, $this);
                }
            }

            $this->validate();
            $this->validateOrders();


        }

        /** Различные странные ситуации, которые надо проверить
         * @throws ValidateException
         */
        private function validate() {

            // Если комментарии в заказах отличаются
            if (count(array_unique(array_map(function($elem) {return $elem->comment; }, $this->orders))) > 1) throw new ValidateException('Комментарии в заказах не идентичны');

        }

        /** Предвалидация для активации кнопки "Оформить"
         *
         */
        private function validateOrders() {
            foreach ($this->orders as &$order) {
                if (!$order->delivery->use_user_address && $order->delivery->point === null) $order->validationErrors[] = new ValidationError('Не указана точка самовывоза');
//                if ($order->delivery->use_user_address && $this->user_info->isAddressValid()) $order->validationErrors[] = new ValidationError('Не указан адрес доставки');
            }
        }

    }

    class ValidateException extends \Exception {

    }


}

namespace Model\OrderDelivery\Entity {

    use Model\OrderDelivery\Error;
    use Model\OrderDelivery\ValidateException;

    class DeliveryGroup {
        /** @var string */
        public $id;
        /** @var string */
        public $name;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
        }
    }

    class DeliveryMethod {
        /** @var string */
        public $token;
        /** @var string */
        public $type_id;
        /** @var string */
        public $name;
        /** @var string */
        public $point_token;
        /** @var string */
        public $group_id;

        public function __construct(array $data = []) {
            if (isset($data['token'])) $this->token = (string)$data['token'];
            if (isset($data['type_id'])) $this->type_id = (string)$data['type_id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['point_token'])) $this->point_token = (string)$data['point_token'];
            if (isset($data['group_id'])) $this->group_id = (string)$data['group_id'];
            if (isset($data['description'])) $this->description = (string)$data['description'];
        }
    }

    class Point {
        /** @var string */
        public $token;
        /** @var string */
        public $action_name;
        /** @var string */
        public $block_name;
        /** @var string|null */
        public $dropdown_name;
        /** @var Point\Shop[]|Point\Pickpoint[]|Point\Svyaznoy[] */
        public $list = [];
        /* @var string */
        public $icon;
        /** @var array */
        public $marker = [
            'iconImageSize' => [28, 39],
            'iconImageOffset' => [-14, -39]
        ];

        public function __construct(array $data = []) {
            if (isset($data['token'])) $this->token = (string)$data['token'];
            if (isset($data['action_name'])) $this->action_name = (string)$data['action_name'];
            if (isset($data['block_name'])) $this->block_name = (string)$data['block_name'];
            if (isset($data['list']) && is_array($data['list'])) {
                foreach ($data['list'] as $item) {
                    if (!isset($item['id'])) continue;

                    switch ($this->token) {
                        case 'self_partner_pickpoint_pred_supplier':
                        case 'self_partner_pickpoint':
                            $this->list[(string)$item['id']] = new Point\Pickpoint($item);
                            break;
                        case 'self_partner_svyaznoy_pred_supplier':
                        case 'self_partner_svyaznoy':
                        case 'shops_svyaznoy':
                            $this->list[(string)$item['id']] = new Point\Svyaznoy($item);
                            break;
                        default:
                            $this->list[(string)$item['id']] = new Point\Shop($item);
                    }
                }
            }
            if ($this->token) {
                switch ($this->token) {
                    case 'self_partner_pickpoint_pred_supplier':
                    case 'self_partner_pickpoint':
                        $this->marker['iconImageHref'] = '/images/deliv-icon/pickpoint.png';
                        $this->icon = '/images/deliv-logo/pickpoint.png';
                        $this->dropdown_name = 'Постаматы Pickpoint';
                        break;
                    case 'self_partner_svyaznoy_pred_supplier':
                    case 'self_partner_svyaznoy':
                    case 'shops_svyaznoy':
                        $this->marker['iconImageHref'] = '/images/deliv-icon/svyaznoy.png';
                        $this->icon = '/images/deliv-logo/svyaznoy.png';
                        $this->dropdown_name = 'Магазины Связной';
                        break;
                    default:
                        $this->marker['iconImageHref'] = '/images/deliv-icon/enter.png';
                        $this->icon = '/images/deliv-logo/enter.png';
                        $this->dropdown_name = 'Магазины Enter';
                }
            }
        }
    }

    class Order {
        /** Внутренний идентификатор
         * @var string
         */
        public $id;
        /** Идентификатор заказа
         * @var string
         */
        public $block_name;
        /** Продавец
         * @var Order\Seller|null
         */
        public $seller;
        /** Массив продуктов заказа
         * @var Order\Product[]
         */
        public $products = [];
        /** Массив скидок
         * @var Order\Discount[]
         */
        public $discounts = [];
        /** @var array */
        public $actions = [];
        /** Выбранный способ доставки
         * @var Order\Delivery|null
         */
        public $delivery;
        /** Выбранная группа доставки
         * @var int|null
         */
        public $delivery_group_id;
        /** Выбранный метод оплаты
         * @var int|null
         */
        public $payment_method_id;
        /** Возможные методы оплаты
         * @var array
         */
        public $payment_methods = [];
        /** Возможные методы доставки
         * @var array
         */
        public $possible_deliveries = [];
        /** Возможные группы доставки
         * @var DeliveryGroup[]
         */
        public $possible_delivery_groups = [];
        /** Возможные методы оплаты
         * @var PaymentMethod[]
         */
        public $possible_payment_methods = [];
        /** Возможные дни доставки
         * @var int[]
         */
        public $possible_days = [];
        /** Возможные интервалы доставки
         * @var array
         */
        public $possible_intervals = [];
        /**
         * Возможные точки самовывоза
         * @var array
         */
        public $possible_points = [];
        /** Возможные точки самовывоза (ссылка на Point)
         * @var Point
         */
        public $points;
        /** Стоимость заказа (со скидками)
         * @var float
         */
        public $total_cost;
        /** Стоимость заказа (без скидок)
         * @var float
         */
        public $total_original_cost;
        /** @var string */
        public $comment = '';
        /** @var Error[] */
        public $errors = [];
        /** @var ValidationError[] */
        public $validationErrors = [];
        /** @var array  */
        public $certificate = [
            'code'  => null,
            'pin'   => null,
            'par'   => null
        ];

        public function __construct(array $data = [], \Model\OrderDelivery\Entity &$orderDelivery = null) {

            $this->id = uniqid();

            if (isset($data['block_name'])) $this->block_name = (string)$data['block_name'];

            if (isset($data['seller']['name'])) $this->seller = new Order\Seller($data['seller']);

            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $item) {
                    $this->products[] = new Order\Product($item);
                }
            }

            if (isset($data['discounts']) && is_array($data['discounts'])) {
                foreach ($data['discounts'] as $item) {
                    if ($item['type'] === 'delivery') continue; // не должны отображать скидку на доставку
                    $this->discounts[] = new Order\Discount($item);
                }
            }

            if (isset($data['actions']) && is_array($data['actions'])) $this->actions = $data['actions'];

            if (isset($data['delivery']['delivery_method_token'])) $this->delivery = new Order\Delivery($data['delivery'], $orderDelivery);

            if (isset($data['payment_method_id'])) $this->payment_method_id = (int)$data['payment_method_id'];

            if (isset($data['possible_deliveries']) && is_array($data['possible_deliveries'])) {
                foreach ($data['possible_deliveries'] as $token) {
                    if (isset($orderDelivery->delivery_methods[$token])) $this->possible_deliveries[] = &$orderDelivery->delivery_methods[$token];
                    else throw new \Exception('Для заказа нет доступного метода доставки');
                }
            }

            if (isset($data['possible_payment_methods']) && is_array($data['possible_payment_methods'])) {
//                $this->possible_payment_methods = (array)$data['possible_payment_methods'];
                foreach ($data['possible_payment_methods'] as $id) {
                    if (isset($orderDelivery->payment_methods[$id])) $this->possible_payment_methods[$id] = &$orderDelivery->payment_methods[$id];
                    else throw new \Exception('Не существует метода оплаты для заказа');
                }
            }

            if (isset($data['possible_days']) && is_array($data['possible_days'])) {
                $this->possible_days = (array)$data['possible_days'];
                if (count($this->possible_days) == 0) throw new \Exception('Не существует доступных дней');
            }

            if (isset($data['possible_intervals']) && is_array($data['possible_intervals'])) $this->possible_intervals = (array)$data['possible_intervals'];

            if (isset($data['total_cost'])) $this->total_cost = (float)$data['total_cost'];
            if (isset($data['total_original_cost'])) $this->total_original_cost = (float)$data['total_original_cost'];

            if (isset($data['possible_point_data']) && is_array($data['possible_point_data'])) {
                foreach ($data['possible_point_data'] as $pointType => $points) {
                    if (is_array($points)) {
                        foreach ($points as $pointItem) {
                            if (
                                !isset($pointItem['id'])
                                || empty($pointItem['nearest_day'])
                                || !isset($orderDelivery->points[$pointType]->list[$pointItem['id']])
                            ) {
                                continue;
                            }

                            $point = [
                                'point'         => &$orderDelivery->points[$pointType]->list[$pointItem['id']],
                                'nearestDay'    => $pointItem['nearest_day'],
                                'cost'          => (int)$pointItem['cost']
                            ];

                            $this->possible_points[$pointType][] =  $point;
                        }
                    }
                }
            }

            $possible_delivery_groups_ids = array_unique(array_map(function ($delivery) { return $delivery->group_id; }, $this->possible_deliveries));
            sort($possible_delivery_groups_ids);

            if (is_array($possible_delivery_groups_ids)) {
                foreach ($possible_delivery_groups_ids as $id) {
                    if (isset($orderDelivery->delivery_groups[$id])) $this->possible_delivery_groups[] = &$orderDelivery->delivery_groups[$id];
                    else throw new \Exception ('Для заказа не найдена группа доставки');
                }
            }

            $this->delivery_group_id = (int)$orderDelivery->delivery_methods[$this->delivery->delivery_method_token]->group_id;

            if (isset($data['comment'])) $this->comment = (string)$data['comment'];

            if (isset($data['certificate'])) {
                if (isset($data['certificate']['code'])) $this->certificate['code'] = (string)$data['certificate']['code'];
                if (isset($data['certificate']['pin']))  $this->certificate['pin'] = (string)$data['certificate']['pin'];
                if (isset($data['certificate']['par']))  $this->certificate['par'] = (string)$data['certificate']['par'];
            }

        }

        /** Это заказ партнерский?
         * @return bool
         */
        public function isPartnerOffer() {
            return $this->seller instanceof Order\Seller && $this->seller->ui != Order\Seller::UI_ENTER;
        }
    }

    class PaymentMethod {
        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $description;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['description'])) $this->description = (string)$data['description'];
        }
    }

    class UserInfo {
        /** @var string */
        public $phone;
        /** @var string|null */
        public $first_name;
        /** @var string|null */
        public $last_name;
        /** @var string|null */
        public $email;
        /** @var array */
        public $address = [
            'street' => null,
            'building'  => null,
            'number'  => null,
            'apartment'  => null,
            'metro_station'  => null,
            'kladr_id'  => null
        ];
        /** @var string */
        public $bonus_card_number;

        public function __construct($arr) {

            if (isset($arr['phone']) && $arr['phone'] != '') {
                $phone = preg_replace('/[^0-9]/', '', $arr['phone']);
                if (strlen($phone) != 11 ) throw new ValidateException('Неправильный формат номера телефона');
                $this->phone = $phone;
            } else {
                //throw new ValidateException('Отсуствует номер телефона');
            }

            if (isset($arr['first_name'])) $this->first_name = (string)$arr['first_name'];
            if (isset($arr['last_name'])) $this->last_name = (string)$arr['last_name'];
            if (isset($arr['email'])) $this->email = (string)$arr['email'];

            if (isset($arr['address']) && is_array($arr['address'])) {
                if (isset($arr['address']['street'])) $this->address['street'] = $arr['address']['street'];
                if (isset($arr['address']['building'])) $this->address['building'] = $arr['address']['building'];
                if (isset($arr['address']['number'])) $this->address['number'] = $arr['address']['number'];
                if (isset($arr['address']['apartment'])) $this->address['apartment'] = $arr['address']['apartment'];
                if (isset($arr['address']['metro_station'])) $this->address['metro_station'] = $arr['address']['metro_station'];
                if (isset($arr['address']['kladr_id'])) $this->address['kladr_id'] = $arr['address']['kladr_id'];
            }

            if (isset($arr['bonus_card_number']) && $arr['bonus_card_number'] !== '') $this->bonus_card_number = (string)$arr['bonus_card_number'];

        }

        public function isAddressValid() {
            return (bool)$this->address['street'] && (bool)$this->address['building'];
        }
    }

    class Subway {
        /** @var string */
        public $name;
        /** @var string */
        public $line;

        public function __construct(array $data = []) {
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['line']['name'])) $this->line = new Subway\Line($data['line']);
        }
    }

    class ValidationError {
        public $message;
        public function __construct($message) {
            $this->message = $message;
        }
    }
}

namespace Model\OrderDelivery\Entity\Subway {
    class Line {
        /** @var string */
        public $name;
        /** @var string */
        public $color;

        public function __construct(array $data = []) {
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['color'])) $this->color = (string)$data['color'];
        }
    }
}

namespace Model\OrderDelivery\Entity\Point {

    class DefaultPoint {
        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $address;
        /** @var string */
        public $regtime;
        /** @var float */
        public $latitude;
        /** @var float */
        public $longitude;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['address'])) $this->address = (string)$data['address'];
            if (isset($data['regtime'])) $this->regtime = (string)$data['regtime'];
            if (isset($data['latitude'])) $this->latitude = (float)$data['latitude'];
            if (isset($data['longitude'])) $this->longitude = (float)$data['longitude'];
        }
    }

    class Shop extends DefaultPoint {
        /** @var \Model\OrderDelivery\Entity\Subway[] */
        public $subway = [];

        public function __construct(array $data = []) {
            parent::__construct($data);
            if (isset($data['subway']) && is_array($data['subway'])) {
                foreach ($data['subway'] as $item) {
                    $this->subway[] = new \Model\OrderDelivery\Entity\Subway($item);
                }
            }
        }
    }

    class Pickpoint extends DefaultPoint {
        /** @var string */
        public $number;
        /** @var string */
        public $house;

        public function __construct(array $data = []) {
            parent::__construct($data);
            if (isset($data['number'])) $this->number = (string)$data['number'];
            if (isset($data['house'])) $this->house = (string)$data['house'];
        }
    }

    class Svyaznoy extends DefaultPoint {
        public function __construct(array $data = []) {
            parent::__construct($data);
        }
    }
}

namespace Model\OrderDelivery\Entity\Order {

    use Model\OrderDelivery\Entity\Point;
    use Model\OrderDelivery\ValidateException;

    class Seller {

        const UI_ENTER = '24594081-6c68-11e2-a300-e83935c0a4d4';

        /** @var string */
        public $name;
        /** @var string|null */
        public $offer;
        /** @var int|null */
        public $id;
        /** @var string|null */
        public $ui;

        public function __construct(array $data = []) {
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['offer'])) $this->offer = (string)$data['offer'];
            if (isset($data['id'])) $this->id = (int)$data['id'];
            if (isset($data['ui'])) $this->ui = (string)$data['ui'];
        }
    }

    class Product {
        use \Model\MediaHostTrait;
        /** @var int */
        public $id;
        /** @var string */
        public $name;
        /** @var string */
        public $link;
        /** @var string */
        public $prefix;
        /** @var string */
        public $name_web;
        /** @var float */
        public $price;
        /** @var float */
        public $original_price;
        /** @var float */
        public $sum;
        /** @var int */
        public $quantity;
        /** @var string */
        public $image;
        /** @var int */
        public $stock;

        public function __construct(array $data = []) {

            if (isset($data['id'])) {
                $this->id = (int)$data['id'];
            } else {
                throw new \Exception('Не указан id продукта');
            }

            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['url'])) $this->link = (string)$data['url'];
            if (isset($data['name_web'])) $this->name_web = (string)$data['name_web'];
            if (isset($data['prefix'])) $this->prefix = (string)$data['prefix'];
            if (isset($data['price'])) $this->price = (float)$data['price'];
            if (isset($data['original_price'])) $this->original_price = (float)$data['original_price'];
            if (isset($data['sum'])) $this->sum = (float)$data['sum'];

            if (isset($data['quantity'])) {
                $this->quantity = (int)$data['quantity'];
            } else {
                throw new \Exception('Не указано количество продукта');
            }

            if (isset($data['image'])) $this->image = (string)$data['image'];
            if (isset($data['stock'])) $this->stock = (int)$data['stock'];
        }

        /**
         * @param int $size
         * @return null|string
         */
        public function getImageUrl($size = 0) {
            if ($this->image) {
                $urls = \App::config()->productPhoto['url'];

                return $this->getHost() . $urls[$size] . $this->image;
            } else {
                return null;
            }
        }
    }

    class Discount {
        /** @var string */
        public $name;
        /** @var int */
        public $discount;
        /** @var string */
        public $type;
        /** @var string */
        public $number;

        public function __construct(array $data = []) {
            if (isset($data['number'])) $this->number = (string)$data['number'];
            if (isset($data['discount'])) $this->discount = (int)$data['discount'];
            if (isset($data['type'])) $this->type = (string)$data['type'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            $this->validate();
        }

        private function validate() {
            foreach (get_object_vars($this) as $name => $value) {
                // для некоторых скидок может и не быть number (бесплатная доставка)
                if ($this->$name === null && $name !== 'number') throw new ValidateException("Для скидки не указан $name");
            }
        }
    }

    class Delivery {
        /** @var string */
        public $delivery_method_token;
        /** @var Delivery\Point|null */
        public $point;
        /** @var float */
        public $price;
        /** @var \DateTime */
        public $date;
        /** @var array|null */
        public $interval;
        /** @var bool */
        public $use_user_address;
        /** @var \Model\OrderDelivery\Entity\DeliveryMethod */
        public $delivery_method;

        public function __construct(array $data = [], \Model\OrderDelivery\Entity &$orderDelivery = null) {
            if (isset($data['delivery_method_token'])) $this->delivery_method_token = (string)$data['delivery_method_token'];
            if (isset($data['point']['id'])) $this->point = new Delivery\Point($data['point']);
            if (isset($data['price'])) $this->price = (float)$data['price'];
            if (isset($data['date'])) $this->date = \DateTime::createFromFormat('U', $data['date']);
            if (isset($data['interval']) && is_array($data['interval'])) $this->interval = array_merge(['from' => null, 'to' => null], $data['interval']);
            if (isset($data['use_user_address'])) $this->use_user_address = (bool)$data['use_user_address'];

            if ($this->delivery_method_token
                && $orderDelivery
                && isset($orderDelivery->delivery_methods[$this->delivery_method_token])
            ) {
                $this->delivery_method =& $orderDelivery->delivery_methods[$this->delivery_method_token];
            } else {
                throw new \Exception ("Не существует метода доставки для заказа");
            }

            $this->validate($orderDelivery);

        }

        private function validate(\Model\OrderDelivery\Entity &$orderDelivery = null) {
            // Предвыбранная точка приходит с неправильным токеном ( CORE-2367 )
            $uniquePointTokens = array_unique(array_map(function(Point $point){ return $point->token; }, $orderDelivery->points));
            if ($this->point && $this->point->token && !in_array($this->point->token, $uniquePointTokens)) {
                throw new \Exception('CORE: Для одного из заказов указан несуществующий token в списке точек самовывоза');
            }
        }
    }

    class Calendar {

    }

    class CalendarDay {
        public $isAvailable;
        public $day;
        public function __construct() {

        }
    }
}

namespace Model\OrderDelivery\Entity\Order\Delivery {
    class Point {
        /** @var string */
        public $token;
        /** @var string */
        public $id;


        public function __construct(array $data = []) {
            if (isset($data['token'])) $this->token = (string)$data['token'];
            if (isset($data['id'])) $this->id = (string)$data['id'];

        }
    }
}