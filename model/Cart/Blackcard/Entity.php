<?php

namespace Model\Cart\Blackcard;

class Entity {
    /** @var string */
    private $number;
    /** @var string */
    private $name;
    /** @var int */
    private $discountSum;
    /** @var \Exception|null */
    private $error;

    /**
     * @param array $data
     */
    public function __construct(array $data = []) {
        if (array_key_exists('number', $data)) $this->setNumber($data['number']);
        if (array_key_exists('discount_sum', $data)) $this->setDiscountSum($data['discount_sum']);
        if (array_key_exists('name', $data)) $this->setName($data['name']);
        if (array_key_exists('error', $data)) {
            $e = $data['error'];
            if (is_array($e)) {
                $e = array_merge([
                    'code'    => 0,
                    'message' => 'Неизвестная ошибка',
                ], $e);
                $e = new \Exception($e['message'], $e['code']);
            }

            if ($e instanceof \Exception) {
                $this->setError($e);
            }
        }
    }

    /**
     * @param string $number
     */
    public function setNumber($number) {
        $this->number = (string)$number;
    }

    /**
     * @return string
     */
    public function getNumber() {
        return $this->number;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = (string)$name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param int $discountSum
     */
    public function setDiscountSum($discountSum) {
        $this->discountSum = (int)$discountSum;
    }

    /**
     * @return int
     */
    public function getDiscountSum() {
        return $this->discountSum;
    }

    /**
     * @param \Exception|null $error
     */
    public function setError(\Exception $error) {
        $this->error = $error;
    }

    /**
     * @return \Exception|null
     */
    public function getError() {
        return $this->error;
    }

    public static function getErrorMessage($code) {
        $message = null;
        switch ($code) {
            case 1000: case 1005: case 1006: case 1007: case 1008: case 1009: case 1010: case 1011: case 1012: case 1013:
                $message = 'Карты с таким номером не существует';
                break;
            case 1001:
                $message = 'Карта неактивна',
                break;
            case 1002:
                $message = 'Карта уже была использована ранее',
                break;
            case 1014:
                $message = 'Скидка не действует на такие товары в корзине';
                break;
            case 1015:
                $message = 'Слишком высокая общая стоимость товаров в корзине';
                break;
            case 1016:
                $message = 'Слишком низкая общая стоимость товаров в корзине';
                break;
            case 1017:
                $message = 'Слишком много товаров в корзине';
                break;
            case 1018:
                $message = 'Слишком мало товаров в корзине';
                break;
            case 1019:
                $message = 'Слишком много наименований товаров в корзине';
                break;
            case 1020:
                $message = 'Слишком мало наименований товаров в корзине';
                break;
            case 1021:
                $message = 'Скидка не может быть применена сегодня';
                break;
            case 1022:
                $message = 'Карта просрочена';
                break;
            case 2000:
                $message = 'Возможно требуется переопределение метода',
                break;
        }

        return $message;
    }
}
