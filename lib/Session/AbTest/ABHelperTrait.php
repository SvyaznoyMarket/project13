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

    /** Минимальная сумма заказа для Воронежа, Н.Новгорода, Рязани
     * @return bool
     */
    public static function isOrderMinSumRestriction(){
        // SITE-5921
        $notAvailableParentRegions = [
            82, // Москва
            14974, // Москва
            83, // Московская область
        ];

        $notAvailableRegions = [
            108136, // Санкт-Петербург
        ];

        $region = \App::user()->getRegion();
        return (!in_array($region->parentId, $notAvailableParentRegions) && !in_array($region->id, $notAvailableRegions));
    }

    public static function isShowSalePercentage() {
        return !in_array(\App::request()->attributes->get('route'), ['slice.category', 'slice.show'], true) || \App::request()->attributes->get('sliceToken') !== 'all_labels' || \App::abTest()->getTest('salePercentage')->getChosenCase()->getKey() !== 'hide';
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

    /** Новая карточка товара
     * @return bool
     */
    public static function isNewProductPage() {
        return \App::abTest()->getTest('productCard') && \App::abTest()->getTest('productCard')->getChosenCase()->getKey() == 'new';
    }
}