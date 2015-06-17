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

        _gaq.push(['_setCustomVar', 23, 'minOrderSum', '<?= \App::abTest()->isOrderMinSumRestriction() ? 'minOrderSum_enabled' : 'minOrderSum_disabled' ?>', 2]);

        <? /* Маркировка продуктов Marketplace */ ?>
        <? if (isset($product) && $product instanceof \Model\Product\Entity): ?>
            <? if ($product->getSlotPartnerOffer()): ?>
                _gaq.push(['_setCustomVar', 12, 'shop_type', 'marketplace-slot', 3]);
            <? elseif ($product->isOnlyFromPartner() || ($product->getPartnersOffer() && !$product->getIsBuyable())): ?>
                /*  Если товар ТОЛЬКО от партнеров или нет у нас, но есть у партнеров */
                _gaq.push(['_setCustomVar', 12, 'shop_type', 'marketplace', 3]);
                if (console && typeof console.log == 'function') console.log('[Google Analytics] _setCustomVar 11 shop_type marketplace');
            <? endif ?>
        <? endif ?>

        _gaq.push(['_trackPageview']);

        /* Classic Google Analytics */
        (function()
        {   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();

        /* Universal Google Analytics */
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    </script>
<? endif ?>
