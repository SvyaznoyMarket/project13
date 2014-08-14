<? if (\App::config()->googleAnalytics['enabled']):

?>
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

    <? $customVarNum = 1 ?>
    <? foreach (\App::abTest()->getTests() as $test): ?>
        <? $customVarNum++ ?>
        <? if ($test->isActive()): ?>
            _gaq.push(['_setCustomVar', <?= $customVarNum ?>, 'User segment', '<?= $test->getKey() ?>_<?= $test->getChosenCase()->getKey() ?>', 2]);
        <? endif ?>
    <? endforeach ?>

    <? if (\App::abtestJson() && \App::abtestJson()->isActive()) : ?>
        _gaq.push(['_setCustomVar', 1, 'User segment', '<?= \App::abTestJson()->getCase()->getGaEvent() ?>', 2]);
    <? endif ?>

    _gaq.push(['_trackPageview']);
    _gaq.push(['_trackPageLoadTime']);

    <? if (isset($orders) && isset($productsById) && isset($servicesById)): ?>
        <? foreach ($orders as $order): ?>
            <?
            $shop = $order->getShopId() && isset($shopsById[$order->getShopId()]) ? $shopsById[$order->getShopId()] : null;
            $deliveries = $order->getDelivery();
            /** @var $delivery \Model\Order\Delivery\Entity */
            $delivery = reset($deliveries);
            ?>

    _gaq.push(['_addTrans',
            '<?= $order->getNumberErp() ?>', // Номер заказа
            '<?= $shop ? $page->escape($shop->getName()) : '' ?>', // Название магазина (Необязательно)
            '<?= str_replace(',', '.', $order->getPaySum()) ?>', // Полная сумма заказа (дроби через точку)
            '', // налог
            '<?= 0 //$delivery ? $delivery->getPrice() : 0 ?>', // Стоимость доставки (дроби через точку)
            '<?= $order->getCity() ? $page->escape($order->getCity()->getName()) : '' ?>', // Город доставки (Необязательно)
            '', // Область (необязательно)
            '' // Страна (нобязательно)
        ]);

        // _addItem: Номер заказа, Артикул, Название товара, Категория товара, Стоимость 1 единицы товара, Количество товара
            <? foreach ($order->getProduct() as $orderProduct): ?>
                <?
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;

                $categories = $product->getCategory();
                $category = array_pop($categories);
                $rootCategory = array_shift($categories);
                if (!$category || !$rootCategory) continue;

                $categoryName = ($rootCategory && ($rootCategory->getId() != $category->getId()))
                    ? ($rootCategory->getName() . ' - ' . $category->getName())
                    : $category->getName();
                ?>

    _gaq.push(['_addItem', '<?= implode("','", array($order->getNumberErp(), $product->getArticle(), $page->escape($product->getName()), $page->escape($categoryName), $orderProduct->getPrice(), $orderProduct->getQuantity())) ?>']);
            <?php endforeach ?>

            <? foreach ($order->getService() as $orderService): ?>
                <?
                $service = isset($servicesById[$orderService->getId()]) ? $servicesById[$orderService->getId()] : null;
                if (!$service) continue;

                $categories = $service->getCategory();
                $category = array_pop($categories);
                $rootCategory = array_shift($categories);

                $categoryName = ($rootCategory && ($rootCategory->getId() != $category->getId()))
                    ? ($rootCategory->getName() . ' - ' . $category->getName())
                    : $category->getName();
                ?>

    _gaq.push(['_addItem', '<?= implode("','", array($order->getNumberErp(), $service->getToken(), $page->escape($service->getName()), $page->escape($categoryName), $orderService->getPrice(), $orderService->getQuantity())) ?>']);
                <?php endforeach ?>

    _gaq.push(['_trackTrans']);
            <? endforeach ?>
    <? endif ?>

    (function()
    {   var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

</script>
<? endif ?>