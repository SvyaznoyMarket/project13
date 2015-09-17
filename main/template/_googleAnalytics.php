<?
$secondaryEnabled = \App::config()->googleAnalytics['secondary.enabled'];

if (\App::config()->googleAnalytics['enabled']): ?>

    <!-- Universal Google Analytics (async method) -->
    <script async src='//www.google-analytics.com/analytics.js'></script>

    <script type="text/javascript">

        /* Universal init */

        window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;

        <? if (\App::config()->debug): ?>
            /* В debug-режиме добавляем логирование вызовов в панель */
            ga(function() {
                var originalFunction = ga;
                ga = function() {
                    var event = null;
                    if (arguments[0] == 'send') {
                        if (typeof arguments[1] == 'object' && arguments[1] && arguments[1].hitType == 'event') {
                            event = {category: arguments[1].eventCategory, action: arguments[1].eventAction, label: arguments[1].eventLabel, value: arguments[1].eventValue};
                        } else if (arguments[1] == 'event') {
                            event = {category: arguments[2], action: arguments[3], label: arguments[4], value: arguments[5]};
                        }
                    }

                    $(document).trigger('googleAnalyticsCall', [{
                        functionName: 'ga',
                        functionArguments: JSON.stringify(arguments),
                        event: event
                    }]);

                    originalFunction.apply(this, arguments);
                };

                for (var key in originalFunction) {
                    if (originalFunction.hasOwnProperty(key)) {
                        ga[key] = originalFunction[key];
                    }
                }
            });
        <? endif ?>

        /* Создаем Universal-трекер */
        ga( 'create', 'UA-25485956-1', 'enter.ru' ); // основной (premium-аккаунт)

        /* E-commerce plugin */
        ga('require', 'ec');

        /* The display features plugin for analytics.js can be used to enable Advertising Features in Google Analytics,
        such as Remarketing, Demographics and Interest Reporting, and more */
        ga( 'require', 'displayfeatures' );

        // Делаем то же самое, если включен дополнительный трекер
        <? if ($secondaryEnabled) : ?>
            ga( 'create', 'UA-25485956-5', 'enter.ru', { 'name': 'secondary'} ); // дополнительный
            ga('secondary.require', 'ec');
            ga( 'secondary.require', 'displayfeatures' );
        <? endif ?>

        <? if (\App::user()->getEntity()) : ?>
            ga('set', '&uid', '<?= \App::user()->getEntity()->getId() ?>');
            <? if ($secondaryEnabled) : ?>
                ga('secondary.set', '&uid', '<?= \App::user()->getEntity()->getId() ?>');
            <? endif ?>
        <? endif ?>

        /* Общая часть для Classic и Universal */

        /* Регион */
        <? if (\App::user()->getRegion() && \App::user()->getRegion()->getName()) : ?>
            ga('set', 'dimension21', '<?= \App::user()->getRegion()->getName() ?>');
            <? if ($secondaryEnabled) : ?>
                ga('secondary.set', 'dimension21', '<?= \App::user()->getRegion()->getName() ?>');
            <? endif ?>
        <? endif ?>

        /* Авторизованный пользователь */
        ga('set', 'dimension22', '<?= \App::user()->getEntity() ? 1 : 0 ?>');
        <? if ($secondaryEnabled) : ?>
            ga('secondary.set', 'dimension22', '<?= \App::user()->getEntity() ? 1 : 0 ?>');
        <? endif ?>

        /* Минимальная сумма заказа */
        ga('set', 'dimension43', '<?= \App::abTest()->isOrderMinSumRestriction() ? 'minOrderSum_enabled' : 'minOrderSum_disabled' ?>');
        <? if ($secondaryEnabled) : ?>
            ga('secondary.set', 'dimension43', '<?= \App::abTest()->isOrderMinSumRestriction() ? 'minOrderSum_enabled' : 'minOrderSum_disabled' ?>');
        <? endif ?>

        <? /* Маркировка продуктов Marketplace */ ?>
        <? if (isset($product) && $product instanceof \Model\Product\Entity): ?>
            <? if ($product->getSlotPartnerOffer()): ?>
            ga('set', 'dimension32', 'marketplace-slot');
            <? if ($secondaryEnabled) : ?>
                ga('secondary.set', 'dimension32', 'marketplace-slot');
            <? endif ?>
            <? elseif ($product->isOnlyFromPartner() || ($product->getPartnersOffer() && !$product->getIsBuyable())): ?>
            /*  Если товар ТОЛЬКО от партнеров или нет у нас, но есть у партнеров */
            ga('set', 'dimension32', 'marketplace');
            <? if ($secondaryEnabled) : ?>
                ga('secondary.set', 'dimension32', 'marketplace');
            <? endif ?>
            if (console && typeof console.log == 'function') console.log('[Google Analytics] _setCustomVar 11 shop_type marketplace');
        <? endif ?>

            ga('set', 'dimension41', '<?= $product->getNumReviews() ? 'Yes' : 'No' ?>');
            <? if ($secondaryEnabled) : ?>
                ga('secondary.set', 'dimension41', '<?= $product->getNumReviews() ? 'Yes' : 'No' ?>');
            <? endif ?>
        <? endif ?>

        <? /* customVar (classic): Слот 1 занят под регион, а слоты 3, 4, 5 заняты под нужды сотрудников отдела аналитики */ ?>
        <? /* dimension (universal): 1-20 под аналитику, 21+ АБ-тесты - customVar_index + 20 */ ?>
        <? foreach (\App::abTest()->getTests() as $test): ?>
            <? if ($test->isActive() && $test->gaSlotNumber): ?>
                ga('set', '<?= sprintf('dimension%s', $test->gaSlotNumber + 20) ?>', '<?= $test->getChosenCase()->getKey() ?>');
                <? if ($secondaryEnabled) : ?>
                    ga('secondary.set', '<?= sprintf('dimension%s', $test->gaSlotNumber + 20) ?>', '<?= $test->getChosenCase()->getKey() ?>');
                <? endif ?>
            <? endif ?>
        <? endforeach ?>


    </script>

<? endif ?>
