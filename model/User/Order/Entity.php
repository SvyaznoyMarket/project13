<?php


namespace Model\User\Order;


use Model\Order\StatusEntity;

class Entity extends \Model\Order\Entity {

    /**
     * @var array
     */
    protected $payment_status_relation = [
        1 => 'Не оплачен',
        2 => 'Оплачен',
        3 => 'Частично оплачен',
        4 => 'Начало оплаты',
        5 => 'Отмена оплаты'
    ];

    /**
     * Возвращает название типа доставки
     * @return string
     */
    public function getDeliveryTypeName() {
        $deliveryType = \RepositoryManager::deliveryType()->getEntityById($this->getDeliveryTypeId());

        return $deliveryType ? $deliveryType->getShortName() : '';
    }

    /**
     * @deprecated
     * @return bool
     */
    public function isCompleted() {
        return $this->status && in_array($this->status->id, [StatusEntity::ID_READY]);
    }

    /**
     * Возвращает массив id продуктов
     * @return array
     */
    public function getAllProductsIds() {
        return array_map( function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $this->getProduct() );
    }

    /**
     * @return string 'завтра, 15 июня 2004, среда'
     */
    public function getDeliveryDate() {
        $date = '';
        if ($this->getDelivery() && $this->getDelivery()->getDeliveredAt()) {
            $deliveryTimestamp = $this->getDelivery()->getDeliveredAt()->getTimestamp();
            // TODO сегодня, завтра, послезавтра
            $date = str_replace(['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                    ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
                    strftime('%e %B %Y, ', $deliveryTimestamp)) . mb_strtolower(strftime('%A', $deliveryTimestamp));
        }
        return $date;
    }

    /**
     * @param $id int
     * @return \Model\Order\Product\Entity|false
     */
    public function getProductById($id) {
        $products = array_filter($this->getProduct(), function ($prod) use ($id) {
            /** @var $prod \Model\Order\Product\Entity */
            return $prod->getId() == $id;
        });
        return reset($products);
    }

    /**
     * @param $payment_status_id
     * @return string
     */
    public function getPaymentStatusText($payment_status_id) {
        if (isset($this->payment_status_relation[$payment_status_id])) {
            return $this->payment_status_relation[$payment_status_id];
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getStatusText() {
        return (string)($this->status ? $this->status->name : '');
    }

} 