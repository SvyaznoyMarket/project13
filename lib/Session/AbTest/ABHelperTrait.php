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

    /**
     * SITE-6016, SITE-6062
     * @return bool
     */
    public static function isOrderDeliveryTypeTestAvailableInCurrentRegion() {
        return in_array(\App::user()->getRegion()->id, [
            14974, // Москва
            108136, // Санкт-Петербург
            83210, // Брянск
            96423, // Владимир
            18074, // Воронеж
            124229, // Казань
            148110, // Калуга
            74562, // Курск
            99, // Липецк
            99958, // Нижний Новгород
            18073, // Тверь
            74358, // Тула
            124232, // Чебоксары
            93746, // Ярославль
            13241, // Белгород
            93747, // Иваново
            88434, // Смоленск
            13242, // Орел
            83209, // Тамбов
            10374, // Рязань

            119623, // Ростов-на-Дону
            124201, // Саратов
            124190, // Краснодар
        ]);
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

    /**
     * Ядерная корзина
     * @return bool
     */
    public static function isCoreCart() {
        return 'enabled' === \App::abTest()->getTest('core_cart')->getChosenCase()->getKey();
    }

    /**
     * Корзина в заказе
     * @return bool
     */
    public static function isOrderWithCart() {
        return 'enabled' === \App::abTest()->getTest('order_with_cart')->getChosenCase()->getKey();
    }

    /**
     * Скидка в рублях
     * @return bool
     */
    public static function isCurrencyDiscountPrice() {
        return 'currency' === \App::abTest()->getTest('discount_price')->getChosenCase()->getKey();
    }
}