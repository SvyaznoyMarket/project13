<?php


namespace Model\PaymentMethod {


    use Model\PaymentMethod\PaymentGroup\PaymentGroupEntity;
    use Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

    class PaymentEntity {

        /** @var PaymentGroupEntity[] */
        public $groups = [];
        /** @var PaymentMethodEntity[] */
        public $methods = [];

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
                    if ($method['id'] == PaymentMethodEntity::PAYMENT_SVYAZNOY_CLUB && \App::request()->cookies->get('enable_sv') != 1) continue; // TODO удалить после теста на бою (тестирование списания баллов Связного Клуба)
                    $this->methods[$method['id']] = new PaymentMethodEntity($method, $this->groups);
                }
            } else {
                throw new \Exception('Нет методов оплаты');
            }
        }


        /**
         * @return \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[]|[]
         */
        public function getOnlineMethods(){
            return array_values(array_filter($this->methods, function(PaymentMethodEntity $methodEntity){ return $methodEntity->isOnline; }));
        }

        /**
         * @return bool
         */
        public function hasSvyaznoyClub() {
            return (bool)$this->methods[PaymentMethodEntity::PAYMENT_SVYAZNOY_CLUB];
        }

        public function unsetSvyaznoyClub() {
            unset($this->methods[PaymentMethodEntity::PAYMENT_SVYAZNOY_CLUB]);
        }

    }

}
