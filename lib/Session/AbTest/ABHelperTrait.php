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
        return !in_array(\App::request()->routeName, ['slice'], true) || \App::request()->routePathVars->get('sliceToken') !== 'all_labels' || \App::abTest()->getTest('salePercentage')->getChosenCase()->getKey() !== 'hide';
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
     * Забирать рекомендации от RichRelevance
     * @return bool
     */
    public static function isRichRelRecommendations()
    {
        return true;
        //return 'richrelevance' === \App::abTest()->getTest('recommendation_source')->getChosenCase()->getKey();
    }

    /**
     * @return bool
     */
    public function isOrderWithDeliveryInterval() { // SITE-6435
        return 'enabled' === \App::abTest()->getTest('show_order_delivery_interval')->getChosenCase()->getKey();
    }

    /**
     * @return bool
     */
    public function isHiddenDeliveryInterval() { // SITE-6667
        return 'hidden' === \App::abTest()->getTest('show_order_delivery_interval')->getChosenCase()->getKey();
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

    public function checkForFreeDelivery() {
        return 'enabled' === \App::abTest()->getTest('check_for_free_delivery_discount')->getChosenCase()->getKey();
    }

    /**
     * Вид обратного звонка с сайта: колокольчик, телефонная трубка, отключен
     *
     * @return string
     */
    public function getCallbackStatus() {
        return \App::abTest()->getTest('callback')->getChosenCase()->getKey();
    }
}