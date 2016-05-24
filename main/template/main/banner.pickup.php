<div class="header-slider js-header-slider">
    <div class="header-slider__inner">
        <div class="header-slider__ctrl">
            <a class="header-slider__ctrl-btn js-header-slider-btn js-header-slider-btn-prev" href="#"></a>
            <a class="header-slider__ctrl-btn header-slider__ctrl-btn_next js-header-slider-btn js-header-slider-btn-next" href="#"></a>
        </div>
        <div class="header-slider__block-items">
            <div class="header-slider__list js-header-slider-items-block">
            <?
                // SITE-5853, SITE-6062

                $helper = new \Helper\TemplateHelper();
                $region = \App::user()->getRegion();
                $pathInfo = \App::request()->getPathInfo();
            ?>

                <? if (in_array($region->id, [
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
                ])): ?>
                    <span class="header-slider__bann js-header-slider-item">
                        Бесплатные <a href="/dostavka">доставка</a> домой и в офис и <a href="/shops">самовывоз</a> из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?> <? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>
                        <span class="header-slider__small">Для заказов от 2990 <span class="rubl">p</span></span>
                    </span>
                <? elseif (in_array($region->id, [
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
                ])): ?>
                    <? if ('/dostavka' !== $pathInfo): ?>
                        <a class="header-slider__bann" href="/dostavka">
                            Бесплатная доставка домой и в офис <? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>
                            <span class="header-slider__small">Для заказов от 2990 <span class="rubl">p</span></span>
                        </a>
                    <? endif ?>
                <? elseif (in_array($region->parentId, [
                    82, // Москва
                    14974, // Москва
                    83, // Московская область

                    14975, // Санкт-Петербург г
                    39, // Санкт-Петербург г
                    108136, // Санкт-Петербург
                    34, // Ленинградская обл

                    73, // Белгородская обл
                    74, // Брянская обл
                    75, // Владимирская обл
                    76, // Воронежская обл
                    77, // Ивановская обл
                    78, // Калужская обл
                    79, // Костромская обл
                    80, // Курская обл
                    81, // Липецкая обл
                    18, // Нижегородская обл
                    84, // Орловская обл
                    98, // Рязанская обл
                    86, // Смоленская обл
                    87, // Тамбовская обл
                    88, // Тверская обл
                    89, // Тульская обл
                    90, // Ярославская обл
                    27, // Чувашская Республика - Чувашия
                    24, // Татарстан Респ
                ])): ?>
                    <? if ('/shops' !== $pathInfo && $region->pointCount): ?>
                        <a class="header-slider__bann" href="/shops">
                            Бесплатный самовывоз из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?><? if ($region->names->locativus): ?> в <?= $helper->escape($region->names->locativus) ?><? endif ?>.
                            <span class="header-slider__small">Для заказов от 2990 <span class="rubl">p</span></span>
                        </a>
                    <? endif ?>
                <? endif ?>

                <a class="header-slider__bann js-header-slider-item" href="/how_pay">
                    Оплачивать заказы стало проще!   Платите удобным способом   <span class="payments-bann"><img src="/images/payments-bann.png"></span>
                </a>

                <span class="header-slider__bann js-header-slider-item">
                    Новый личный кабинет. Контролируй свои заказы. <a href="<?= $page->url('user.login') ?>" class="header-slider__login js-login-opener">Войти</a>
                </span>
            </div>
        </div>
    </div>
</div>