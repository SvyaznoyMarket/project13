<?php


namespace Session\AbTest;

/** Трейт, помогающий определять вариант АБ-теста у пользователя
 * Class ABHelperTrait
 * @package Session\AbTest
 */
trait ABHelperTrait {

    public static function isNewFurnitureListing() {
        return \App::abTest()->getTest('furnitureListing')->getChosenCase()->getKey() !== 'old';
    }

    /** Поиск с возможностью фильтрации по категориям?
     * @return bool
     */
    public static function isAdvancedSearch(){
        return \App::abTest()->getTest('adv_search')->getChosenCase()->getKey() == 'on';
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

    /**
     * @return int
     */
    public static function getGiftButtonNumber(){
        $key = \App::abTest()->getTest('giftButton')->getChosenCase()->getKey();
        if ($key === 'default') {
            return 1;
        } else {
            return $key;
        }
    }

    /** Открытие ссылок на товары в новом окне
     * @return bool
     */
    public static function isNewWindow(){
        return \App::abTest()->getTest('new_window')->getChosenCase()->getKey() == 'on';
    }

    /**
     * Ядерная корзина
     * @deprecated
     * @return bool
     */
    public static function isCoreCart() {
        // TODO: выпилить
        return true;
    }

    /**
     * Старый личный кабинет
     * @return bool
     */
    public static function isOldPrivate() {
        return false;
    }

    /**
     * Корзина в заказе
     * @return bool
     */
    public static function isOrderWithCart() {
        return false; //'enabled' === \App::abTest()->getTest('order_with_cart')->getChosenCase()->getKey();
    }

    /**
     * Скидка в рублях
     * @return bool
     */
    public static function isCurrencyDiscountPrice() {
        return 'currency' === \App::abTest()->getTest('discount_price')->getChosenCase()->getKey();
    }

    /**
     * Текст кнопки "Оформить заказ" в параплашке изменен на "В корзину"?
     * @return bool
     */
    public function isCartTextInOrderButton() {
        return self::isOrderWithCart() && ('cart' === \App::abTest()->getTest('cart_text')->getChosenCase()->getKey());
    }

    public function getOrderDeliveryType() {
        $key = \App::abTest()->getTest('order_delivery_type')->getChosenCase()->getKey();
        if (!in_array($key, ['self', 'delivery'], true)) {
            return null;
        }

        // SITE-6016, SITE-6062
        if (!in_array(\App::user()->getRegion()->id, [
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
            13242, // Орел
            83209, // Тамбов
            10374, // Рязань

            88434, // Смоленск
            119623, // Ростов-на-Дону
            124201, // Саратов
            124190, // Краснодар
            93751, // Екатеринбург
            124217, // Ставрополь
            93749, // Самара
            143707, // Волгоград
            93752, // Челябинск
            93748, // Уфа
            152595, // Вологда
            124216, // Псков
            124226, // Оренбург
            124230, // Ижевск
            124227, // Пенза
            124231, // Ульяновск
            78637, // Великий Новгород
            124224, // Йошкар-Ола
            124213, // Петрозаводск
            124223, // Киров
            124225, // Саранск
        ])) {
            return null;
        }

        return $key;
    }

    public function isInfinityScroll() {
        return 'on' === \App::abTest()->getTest('infinity_scroll')->getChosenCase()->getKey();
    }

    /**
     * @return bool
     */
    public static function isOneClickOnly() {
        $config = \App::config();
        $user = \App::user();

        return
            (true === $config->cart['oneClickOnly'])
            && ($config->region['defaultId'] === $user->getRegion()->id)
            //&& !$user->getCart()->count()
        ;
    }

    /**
     * @return bool
     */
    public function isOrderWithDeliveryInterval() { // SITE-6435
        return 'enabled' === \App::abTest()->getTest('show_order_delivery_interval')->getChosenCase()->getKey();
    }

    /**
     * Дизайн блока "Вы смотрели" на главной
     * @return int
     */
    public function getViewedOnMainCase()
    {
        switch (\App::abTest()->getTest('viewed_on_main')->getChosenCase()->getKey()) {
            case 'default_view':
                return 1;
            case 'modern_view':
                return 2;
            case 'season_view':
                return 3;
            case 'disabled':
            default:
                return 0;
        }
    }

    /**
     * @return string
     */
    public function getOrderButtonLocation() {
        return \App::abTest()->getTest('order_button_location')->getChosenCase()->getKey();
    }

    /**
     * @return string
     */
    public function getOneClickView() {
        return 'default';
//        return \App::abTest()->getTest('1click_view')->getChosenCase()->getKey();
    }

    public function checkForFreeDelivery() {
        return 'enabled' === \App::abTest()->getTest('check_for_free_delivery_discount')->getChosenCase()->getKey();
    }
}