<?php


namespace Model\User\Order;


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
     * @var array
     */
    protected $status_id_relation = [
        10 => "Новый",
        1 => "Ваш заказ принят в обработку",
        3 => "Заказ размещен у поставщика",
        4 => "Заказ передан в доставку",
        20 => "Заказ готов к передаче",
        5 => "Получен",
        100 => "Отменен",
    ];

    /**
     * @var LifecycleEntity[]|null
     */
    public $lifecycle;

    public function __construct($data) {
        parent::__construct($data);
        if (isset($data['lifecycle'])) $this->setLifecycle($data['lifecycle']);
    }

    /**
     * Возвращает название типа доставки
     * @return string
     */
    public function getDeliveryTypeName() {
        $deliveryType = \RepositoryManager::deliveryType()->getEntityById($this->getDeliveryTypeId());

        return $deliveryType ? $deliveryType->getShortName() : '';
    }

    /**
     * @param mixed $data
     */
    public function setLifecycle($data)
    {
        if (is_array($data)) {
            foreach ($data as $cycle) {
                $this->lifecycle[] = new LifecycleEntity($cycle);
            }
        } else {
            $this->lifecycle = [];
        }

    }

    /**
     * @return LifecycleEntity[]|null
     */
    public function getLifecycle()
    {
        return $this->lifecycle;
    }

    /**
     * Возвращает title первого false-статуса
     * @return string
     */
    public function getLastLifecycleStatus() {
        $status = '';
        if ($this->lifecycle && is_array($this->lifecycle)) {
            foreach ($this->lifecycle as $cycle) {
                if (!$cycle->getCompleted()) break;
                $status = $cycle->getTitle();
            }
        }
        return $status;
    }

    /**
     * Возвращает статус последнего lifecycle-а
     * @return bool
     */
    public function isCompleted() {
        $status = true;
        if ($this->lifecycle && is_array($this->lifecycle)) {
            $status = $this->lifecycle[count($this->lifecycle)-1]->getCompleted();
        }
        return $status;
    }

    /**
     * Возвращает ссылку на добавление массива товаров
     * @return string
     */
    public function addProductsToCartLink() {
        $products = [];
        foreach ($this->getProduct() as $product) {
            $products['product'][] = [
                'id' => $product->getId(),
                'quantity' => $product->getQuantity()
            ];
        }
        return \App::router()->generate('cart.product.setList', $products );
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
     * @param $statusId
     * @return string
     */
    public function getStatusText($statusId) {
        if (isset($this->status_id_relation[$statusId])) {
            return $this->status_id_relation[$statusId];
        } else {
            return '';
        }
    }

} 