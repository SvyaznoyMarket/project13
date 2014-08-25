<?php


namespace Model\PaymentMethod {


    use Model\PaymentMethod\PaymentGroup\PaymentGroupEntity;
    use Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

    class PaymentEntity {

        /** @var PaymentGroupEntity[] */
        public $groups;
        /** @var PaymentMethodEntity[] */
        public $methods;

        public function __construct($arr) {

            if (isset($arr['groups']) && is_array($arr['groups'])) {
                foreach ($arr['groups'] as $key => $group) {
                    $this->groups[(string)$key] = new PaymentGroupEntity($group);
                }
            } else {
                throw new \Exception('Нет групп оплаты');
            }

            if (isset($arr['methods']) && is_array($arr['methods'])) {
                foreach ($arr['methods'] as $method) {
                    $this->methods[$method['id']] = new PaymentMethodEntity($method, $this->groups);
                }
            } else {
                throw new \Exception('Нет методов оплаты');
            }
        }

    }

}
