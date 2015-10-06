<?php


namespace Model\PaymentMethod\PaymentMethod;


class PaymentMethodEntity {

    const PAYMENT_CASH = 1;
    const PAYMENT_CARD_ON_DELIVERY = 2;
    const PAYMENT_CARD_ONLINE = 5;
    const PAYMENT_CREDIT = 6;
    const PAYMENT_PSB = 8;
    const PAYMENT_CERTIFICATE = 10;
    const PAYMENT_PAYPAL = 13;
    const PAYMENT_SVYAZNOY_CLUB = 14;

    /** @var int */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $description;
    /** @var bool */
    public $isCredit;
    /** @var bool */
    public $isOnline;
    /** @var bool */
    public $isCorporative;
    /** @var bool */
    public $isAvailableToPickpoint;
    /** @var  \Model\PaymentMethod\PaymentGroup\PaymentGroupEntity */
    public $paymentGroup;
    /** Возможные маркетинговые акции
     * @var array */
    public $availableActions = [];

    /** @var string|null */
    public $icon;

    public function __construct($arr, &$groups) {

        if (isset($arr['id'])) {
            $this->id = (int)$arr['id'];
        } else throw new \Exception('Не указан id');

        if (isset($arr['name'])) $this->name = (string)$arr['name'];

        if (isset($arr['description'])) $this->description = (string)$arr['description'];

        if (isset($arr['is_credit'])) $this->isCredit = (bool)$arr['is_credit'];

        if (isset($arr['is_online'])) $this->isOnline = (bool)$arr['is_online'];

        if (isset($arr['is_corporative'])) $this->isCorporative = (bool)$arr['is_corporative'];

        if (isset($arr['available_to_pickpoint'])) $this->isAvailableToPickpoint = (bool)$arr['available_to_pickpoint'];

        if (isset($arr['payment_method_group_id'])) {
            $id = (string)$arr['payment_method_group_id'];
            if(!isset($groups[$id])) throw new \Exception('Для метода нет группы оплаты');
            $this->paymentGroup = $groups[$id];
        } else throw new \Exception('Для метода нет группы оплаты');

        switch ($this->id) {
            case 5: $this->icon = '/styles/order-new/img/payment/pay-card.png'; break;
            case 8: $this->icon = '/styles/order-new/img/payment/pay-psb.png'; break;
            case 11: $this->icon = '/styles/order-new/img/payment/pay-webmoney.png'; break;
            case 12: $this->icon = '/styles/order-new/img/payment/pay-qiwi.png'; break;
            case 13: $this->icon = '/styles/order/img/paypal.png'; break;
            case 14: $this->icon = '/styles/order/img/svyaznoy.png'; break;
            case 16: $this->icon = '/styles/order-new/img/payment/pay-yandex.png'; break;
        }

        if (isset($arr['available_actions']) && is_array($arr['available_actions'])) $this->availableActions = $arr['available_actions'];

    }

    /** Возвращает discount-акцию
     * @param $alias string
     * @return array|null
     */
    public function getAction($alias) {
        foreach ($this->availableActions as $arr) {
            if (isset($arr['alias']) && $alias == $arr['alias']) return $arr;
        }
        return null;
    }

    public function isSvyaznoyClub() {
        return $this->id == self::PAYMENT_SVYAZNOY_CLUB;
    }

} 