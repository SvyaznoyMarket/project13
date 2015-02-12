<?php


namespace Session\AbTest;

/** Трейт, помогающий определять вариант АБ-теста у пользователя
 * Class ABHelperTrait
 * @package Session\AbTest
 */
trait ABHelperTrait {

    /** Новая главная страница?
     * @return bool
     */
    public static function isNewMainPage() {
        return \App::abTest()->getTest('main_page') && in_array(\App::abTest()->getTest('main_page')->getChosenCase()->getKey(), ['new', 'search_new_1', 'search_new_2']);
    }

    /** Возвращает вариант новой главной страницы
     * @return bool|int
     */
    public static function getNewMainPageVar(){
        if (\App::abTest()->getTest('main_page')) {
            switch (\App::abTest()->getTest('main_page')->getChosenCase()->getKey()) {
                case 'search_new_1':
                    return 1;
                case 'search_new_2':
                    return 2;
                default:
                    return 0;
            }
        } else {
            return false;
        }
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
        return (int)$ordersCount == 1 && \App::abTest()->getTest('online_motivation') && \App::abTest()->getTest('online_motivation')->getChosenCase()->getKey() == 'on';
    }

    /** Обязательный email при оформлении заказа?
     * @return bool
     */
    public static function isEmailRequired(){
        return \App::abTest()->getTest('order_email') && \App::abTest()->getTest('order_email')->getChosenCase()->getKey() == 'required';
    }

} 