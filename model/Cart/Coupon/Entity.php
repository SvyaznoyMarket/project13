<?php

namespace Model\Cart\Coupon;

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
            case 300: case 303: case 305: case 306: case 307: case 308: case 309: case 310: case 311: case 312: case 313:
                $message = 'Купона с таким номером не существует';
                break;
            case 301: case 304:
                $message = 'Купон неактивный';
                break;
            case 302:
                $message = 'Купон уже был использован ранее';
                break;
            case 314:
                $message = 'Скидка не действует на такие товары в корзине';
                break;
            case 315:
                $message = 'Слишком высокая общая стоимость товаров в корзине';
                break;
            case 316:
                $message = 'Слишком низкая общая стоимость товаров в корзине';
                break;
            case 317:
                $message = 'Слишком много товаров в корзине';
                break;
            case 318:
                $message = 'Слишком мало товаров в корзине';
                break;
            case 319:
                $message = 'Слишком много наименований товаров в корзине';
                break;
            case 320:
                $message = 'Слишком мало наименований товаров в корзине';
                break;
            case 321:
                $message = 'Скидка не может быть применена сегодня';
                break;
            case 322:
                $message = 'Купон просрочен';
                break;
        }

        return $message;
    }
}
