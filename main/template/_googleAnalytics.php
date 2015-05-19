<? if (\App::config()->googleAnalytics['enabled']): ?>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-25485956-1']);
        <? if ( false == \App::config()->debug ) { ?>
            _gaq.push(['_setDomainName', 'enter.ru']);
        <? } ?>
        _gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
        _gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
        _gaq.push(['_addOrganic', 'nigma.ru', 's']);
        _gaq.push(['_addOrganic', 'webalta.ru', 'q']);
        _gaq.push(['_addOrganic', 'aport.ru', 'r']);
        _gaq.push(['_addOrganic', 'poisk.ru', 'text']);
        _gaq.push(['_addOrganic', 'km.ru', 'sq']);
        _gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
        _gaq.push(['_addOrganic', 'quintura.ru', 'request']);
        _gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
        _gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
        _gaq.push(['_addOrganic', 'gogo.ru', 'q']);
        _gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
        _gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
        _gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
        _gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
        _gaq.push(['_addOrganic', 'ru.search.yahoo.com','p']);
        _gaq.push(['_addOrganic', 'ya.ru', 'q']);
        _gaq.push(['_addOrganic', 'm.yandex.ru','query']);

        <? /* Слот 1 занят под регион, а слоты 3, 4, 5 заняты под нужды сотрудников отдела аналитики */ ?>
        <? foreach (\App::abTest()->getTests() as $test): ?>
            <? if ($test->isActive() && $test->gaSlotNumber): ?>
                _gaq.push(['_setCustomVar', <?= $test->gaSlotNumber ?>, 'User segment', '<?= $test->getKey() ?>_<?= $test->getChosenCase()->getKey() ?>', <?= $test->gaSlotScope ?>]);
            <? endif ?>
        <? endforeach ?>

        <? if (\App::user()->getRegion() && \App::user()->getRegion()->getName()) : ?>
            _gaq.push(['_setCustomVar', 1, 'city', '<?= \App::user()->getRegion()->getName() ?>', 2]);
        <? endif ?>

        _gaq.push(['_setCustomVar', 2, 'authenticated_user', '<?= \App::user()->getEntity() ? 1 : 0 ?>', 3]);

        <? if (isset($product) && $product instanceof \Model\Product\Entity): ?>
            <? if ($product->getSlotPartnerOffer()): ?>
                _gaq.push(['_setCustomVar', 12, 'shop_type', 'marketplace-slot', 3]);
            <? elseif ($product->isOnlyFromPartner() || ($product->getPartnersOffer() && !$product->getIsBuyable())): ?>
                /*  Если товар ТОЛЬКО от партнеров или нет у нас, но есть у партнеров */
                _gaq.push(['_setCustomVar', 12, 'shop_type', 'marketplace', 3]);
                if (console && typeof console.log == 'function') console.log('[Google Analytics] _setCustomVar 11 shop_type marketplace');
            <? endif ?>

            _gaq.push(['_setCustomVar', 21, 'Items_review', '<?= $product->getNumReviews() ? 'Yes' : 'No' ?>', 3]);
        <? endif ?>

        _gaq.push(['_trackPageview']);

        /* Classic Google Analytics */
        (function()
        {   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

            <? if (\App::config()->debug): ?>
                ga.onload = function() {
                    var originalFunction = _gaq.push;
                    _gaq.push = function() {
                        $(document).trigger('googleAnalyticsCall', [{
                            functionName: '_gaq.push',
                            functionArguments: JSON.stringify(arguments),
                            event: arguments[0] && arguments[0][0] == '_trackEvent' ? {category: arguments[0][1], action: arguments[0][2], label: arguments[0][3], value: arguments[0][4]} : null
                        }]);

                        originalFunction.apply(this, arguments);
                    };

                    for (var key in originalFunction) {
                        if (originalFunction.hasOwnProperty(key)) {
                            _gaq.push[key] = originalFunction[key];
                        }
                    }
                };
            <? endif ?>

            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

        /* Universal Google Analytics */
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),

            <? if (\App::config()->debug): ?>
                a.onload = function() {
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
                };
            <? endif ?>

            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    </script>
<? endif ?>
