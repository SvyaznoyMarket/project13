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
        return \App::abTest()->getTest('main_page') && \App::abTest()->getTest('main_page')->getChosenCase()->getKey() == 'new';
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
        /* Область -> parent_id
         * --------------------
         * Воронежская - 76
         * Орловская - 84
         * Тульская - 89
         * Ярославская - 90
         */
        /* Если пользователь попадает в регион теста */
        if (\App::user()->getRegion() && in_array(\App::user()->getRegion()->getParentId(), [76,84,89,90])) {
            return \App::abTest()->getTest('order_delivery_price_2') && \App::abTest()->getTest('order_delivery_price_2')->getChosenCase()->getKey() == 'delivery_self_100';
        }
        return false;
    }

} 