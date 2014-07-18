<?php

namespace Model\OrderDelivery {
    class Entity {
        /** @var Entity\DeliveryGroup[] */
        public $delivery_groups = [];
        /** @var Entity\DeliveryMethod[] */
        public $delivery_methods = [];
        /** @var Entity\Point[] */
        public $points = [];
        /** @var Entity\Order[] */
        public $orders = [];
        /** @var Entity\PaymentMethod[] */
        public $payment_methods = [];
        /** @var Entity\UserInfo|null */
        public $user_info;
        /** @var int */
        public $total_cost;

        public function __construct(array $data = []) {
            if (isset($data['delivery_groups']) && is_array($data['delivery_groups'])) {
                foreach ($data['delivery_groups'] as $item) {
                    if (!isset($item['id'])) continue;

                    $this->delivery_groups[(string)$item['id']] = new Entity\DeliveryGroup($item);
                }
            }
            if (isset($data['delivery_methods']) && is_array($data['delivery_methods'])) {
                foreach ($data['delivery_methods'] as $item) {
                    if (!isset($item['token'])) continue;

                    $this->delivery_methods[(string)$item['token']] = new Entity\DeliveryMethod($item);
                }
            }
            if (isset($data['points']) && is_array($data['points'])) {
                foreach ($data['points'] as $itemToken => $item) {
                    $item['token'] = $itemToken;

                    $this->points[] = new Entity\Point($item);
                }
            }
            if (isset($data['orders']) && is_array($data['orders'])) {
                foreach ($data['orders'] as $item) {
                    $this->orders[] = new Entity\Order($item);
                }
            }
            if (isset($data['payment_methods']) && is_array($data['payment_methods'])) {
                foreach ($data['payment_methods'] as $item) {
                    $this->payment_methods[] = new Entity\PaymentMethod($item);
                }
            }
            if (isset($data['user_info']['phone'])) $this->user_info = new Entity\UserInfo($data['user_info']);
            if (isset($data['total_cost'])) $this->total_cost = (int)$data['total_cost'];
        }
    }
}

namespace Model\OrderDelivery\Entity {
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
        /** @var Point\Shop[] */
        public $list = [];

        public function __construct(array $data = []) {
            if (isset($data['token'])) $this->token = (string)$data['token'];
            if (isset($data['action_name'])) $this->action_name = (string)$data['action_name'];
            if (isset($data['block_name'])) $this->block_name = (string)$data['block_name'];
            if (isset($data['list']) && is_array($data['list'])) {
                foreach ($data['list'] as $item) {
                    if (!isset($item['id'])) continue;

                    switch ($this->token) {
                        case 'shops':
                            $this->list[(string)$item['id']] = new Point\Shop($item);
                            break;
                        case 'pickpoints':
                            $this->list[(string)$item['id']] = new Point\Pickpoint($item);
                            break;
                    }
                }
            }
        }
    }

    class Order {
        /** @var string */
        public $block_name;
        /** @var string */
        public $seller;
        /** @var Order\Product[] */
        public $products = [];
        /** @var Order\Discount[] */
        public $discounts = [];
        /** @var Order\Delivery|null */
        public $delivery;
        /** @var string|null */
        public $payment_method_id;
        /** @var array */
        public $payment_methods = [];
        /** @var array */
        public $possible_deliveries = [];
        /** @var array */
        public $possible_payment_methods = [];
        /** @var array */
        public $possible_days = [];
        /** @var array */
        public $possible_intervals = [];
        /** @var int */
        public $total_cost;

        public function __construct(array $data = []) {
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $item) {
                    $this->products[] = new Order\Product($item);
                }
            }
            if (isset($data['discounts']) && is_array($data['discounts'])) {
                foreach ($data['discounts'] as $item) {
                    $this->discounts[] = new Order\Discount($item);
                }
            }
            if (isset($data['delivery']['delivery_method_token'])) $this->delivery = new Order\Delivery($data['delivery']);
            if (isset($data['payment_method_id'])) $this->payment_method_id = (string)$data['payment_method_id'];
            if (isset($data['possible_deliveries']) && is_array($data['possible_deliveries'])) $this->possible_deliveries = (array)$data['possible_deliveries'];
            if (isset($data['possible_payment_methods']) && is_array($data['possible_payment_methods'])) $this->possible_payment_methods = (array)$data['possible_payment_methods'];
            if (isset($data['possible_days']) && is_array($data['possible_days'])) $this->possible_days = (array)$data['possible_days'];
            if (isset($data['possible_intervals']) && is_array($data['possible_intervals'])) $this->possible_intervals = (array)$data['possible_intervals'];
            if (isset($data['total_cost'])) $this->total_cost = (int)$data['total_cost'];
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
        /** @var string */
        public $first_name;
        /** @var string */
        public $last_name;
        /** @var array */
        public $address = [
            'street' => null,
            'build'  => null,
        ];
    }
}

namespace Model\OrderDelivery\Entity\Point {
    class Shop {
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

    class Pickpoint {
        /** @var string */
        public $id;
        /** @var string */
        public $number;
        /** @var string */
        public $name;
        /** @var string */
        public $address;
        /** @var string */
        public $house;
        /** @var string */
        public $regtime;
        /** @var float */
        public $latitude;
        /** @var float */
        public $longitude;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['number'])) $this->number = (string)$data['number'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['address'])) $this->address = (string)$data['address'];
            if (isset($data['house'])) $this->house = (string)$data['house'];
            if (isset($data['regtime'])) $this->regtime = (string)$data['regtime'];
            if (isset($data['latitude'])) $this->latitude = (float)$data['latitude'];
            if (isset($data['longitude'])) $this->longitude = (float)$data['longitude'];
        }
    }
}

namespace Model\OrderDelivery\Entity\Order {
    class Product {
        /** @var string */
        public $id;
        /** @var string */
        public $name;
        /** @var int */
        public $price;
        /** @var int */
        public $original_price;
        /** @var int */
        public $sum;
        /** @var int */
        public $quantity;
        /** @var string */
        public $image;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['name'])) $this->name = (string)$data['name'];
            if (isset($data['price'])) $this->price = (int)$data['price'];
            if (isset($data['original_price'])) $this->original_price = (int)$data['original_price'];
            if (isset($data['sum'])) $this->sum = (int)$data['sum'];
            if (isset($data['quantity'])) $this->quantity = (int)$data['quantity'];
            if (isset($data['image'])) $this->image = (string)$data['image'];
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
        public $description;

        public function __construct(array $data = []) {
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['discount'])) $this->discount = (int)$data['discount'];
            if (isset($data['type'])) $this->type = (string)$data['type'];
            if (isset($data['description'])) $this->description = (string)$data['description'];
        }
    }

    class Delivery {
        /** @var string */
        public $delivery_method_token;
        /** @var Delivery\Point|null */
        public $point;
        /** @var int */
        public $price;
        /** @var \DateTime */
        public $date;
        /** @var array|null */
        public $interval;
        /** @var bool */
        public $use_user_address;

        public function __construct(array $data = []) {
            if (isset($data['delivery_method_token'])) $this->delivery_method_token = (string)$data['delivery_method_token'];
            if (isset($data['point']['id'])) $this->point = new Delivery\Point($data['point']);
            if (isset($data['price'])) $this->price = (int)$data['price'];
            if (isset($data['date'])) {
                try {
                    $this->date = (new \DateTime())->setTimestamp($data['date']);
                } catch (\Exception $e) {}
            }
            if (isset($data['interval']) && is_array($data['interval'])) $this->interval = array_merge(['from' => null, 'to' => null], $data['interval']);
            if (isset($data['use_user_address'])) $this->use_user_address = (bool)$data['use_user_address'];
        }
    }
}

namespace Model\OrderDelivery\Entity\Order\Delivery {
    class Point {
        /** @var string */
        public $token;
        /** @var string */
        public $id;
        /** @var array */
        public $possible_point_ids = [];

        public function __construct(array $data = []) {
            if (isset($data['token'])) $this->token = (string)$data['token'];
            if (isset($data['id'])) $this->id = (string)$data['id'];
            if (isset($data['possible_point_ids']) && is_array($data['possible_point_ids'])) $this->possible_point_ids = (array)$data['possible_point_ids'];
        }
    }
}