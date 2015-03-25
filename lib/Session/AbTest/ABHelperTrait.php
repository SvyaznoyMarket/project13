<?php


namespace Session\AbTest;

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
        if (\App::config()->self_delivery['enabled'] == false) return false;
        // если нет магазинов в регионе пользователя
        if (\App::user()->getRegion() && !\App::user()->getRegion()->getHasShop()) return false;
        /* Область -> parent_id
         * --------------------
         * Московская - 83
         * Москва - 82
         * Ленинградская - 34
         * Санкт-Петербург - 39
         */
        /* Если пользователь попадает в регион теста */
        if (\App::user()->getRegion() && !in_array(\App::user()->getRegion()->getParentId(), [82,83,34,39])) {
//            return \App::abTest()->getTest('order_delivery_price_2') && \App::abTest()->getTest('order_delivery_price_2')->getChosenCase()->getKey() == 'delivery_self_100';
            return true;
        }
        return false;
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

    /** Обязательный email при оформлении заказа?
     * @return bool
     */
    public static function isEmailRequired(){
        return \App::abTest()->getTest('order_email') && \App::abTest()->getTest('order_email')->getChosenCase()->getKey() == 'required';
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