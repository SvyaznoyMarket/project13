<?php


namespace Session\AbTest;

use Model\Product\Entity;

/** Трейт, помогающий определять вариант АБ-теста у пользователя
 * Class ABHelperTrait
 * @package Session\AbTest
 */
trait ABHelperTrait {

    public static function isNewFurnitureListing() {
        $test = \App::abTest()->getTest('furnitureListing');
        return $test && $test->getChosenCase()->getKey() === 'new';
    }

    /** Поиск с возможностью фильтрации по категориям?
     * @return bool
     */
    public static function isAdvancedSearch(){
        return \App::abTest()->getTest('adv_search') && \App::abTest()->getTest('adv_search')->getChosenCase()->getKey() == 'on';
    }

    /** Платный самовывоз?
     * @return bool
     */
    public static function isSelfPaidDelivery(){
        // SITE-5298, SITE-5425
        return false;
    }

    public static function getColorClass(Entity $product, $location = null){
        // SITE-5394 цвет кнопки купить
        $colorClass = null;
        switch (\App::abTest()->getTest('cart_button_color')->getChosenCase()->getKey()) {
            case 'red':
                $colorClass = ' btnBuy__eLink--red';
                break;
            case 'magenta':
                $colorClass = ' btnBuy__eLink--magenta';
                break;
        }

        if ($location !== 'slider') {
            foreach ($product->getCategory() as $category) {
                // Pandora
                if (in_array($category->getUi(), ['3fe49466-e5cf-4042-963d-025db2142600'])) {
                    $colorClass = null;
                    break;
                }
            }
        }

        return $colorClass;
    }

    /** Онлайн-мотивация при покупке?
     * @param $ordersCount int Количество заказов
     * @return bool
     */
    public static function isOnlineMotivation($ordersCount = 0){
        return (int)$ordersCount == 1
            && \App::abTest()->getTest('online_motivation')
            && in_array(\App::abTest()->getTest('online_motivation')->getChosenCase()->getKey(), ['on', 'online_motivation_coupon', 'online_motivation_discount']);
    }

    /**
     * @return int
     */
    public static function getGiftButtonNumber(){
        $test = \App::abTest()->getTest('giftButton');
        if ($test) {
            $key = $test->getChosenCase()->getKey();
            if ($key === 'default') {
                return 1;
            } else {
                return $key;
            }
        }

        return 1;
    }

    /** Открытие ссылок на товары в новом окне
     * @return bool
     */
    public static function isNewWindow(){
        $test = \App::abTest()->getTest('new_window');
        return $test && $test->getChosenCase()->getKey() == 'on';
    }

    /** Меню-гамбургер (только в карточке товара)
     * @return bool
     */
    public static function isMenuHamburger(){
        $test = \App::abTest()->getTest('new_window');
        return $test && $test->getChosenCase()->getKey() == 'hamburger' && \App::request()->attributes->get('route') == 'product';
    }
}