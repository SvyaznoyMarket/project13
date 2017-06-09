<div class="header-slider js-header-slider">
    <div class="header-slider__inner">
        <? /*
        <div class="header-slider__ctrl">
            <a class="header-slider__ctrl-btn js-header-slider-btn js-header-slider-btn-prev" href="#"></a>
            <a class="header-slider__ctrl-btn header-slider__ctrl-btn_next js-header-slider-btn js-header-slider-btn-next" href="#"></a>
        </div>
        */ ?>
        <div class="header-slider__block-items">
            <div class="header-slider__list js-header-slider-items-block">
                <?
                    $helper = new \Helper\TemplateHelper();
                    $region = \App::user()->getRegion();
                    $pathInfo = \App::request()->getPathInfo();
                ?>

                <? if (in_array($region->id, [
                    14974, // Москва
                ])): ?>
                    <span class="header-slider__bann js-header-slider-item">
                        Бесплатные <a href="/delivery">доставка</a> и <a href="/shops">самовывоз</a>
                        из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?>
                        <img class="header-slider__logo" src="/images/logos/euroset/65x20.png" alt="Евросеть" />
                        <img class="header-slider__logo" src="/images/logos/svyaznoy/65x20.png" alt="Связной" />

                        <span class="header-slider__small">Для заказов от 2990 <span class="rubl">p</span></span>
                    </span>
                <? else: ?>
                    <a class="header-slider__bann" href="/shops">
                        Самовывоз
                        из <?= $region->pointCount ?> <?= $helper->numberChoice($region->pointCount, ['точки', 'точек', 'точек']) ?>
                        <img class="header-slider__logo" src="/images/logos/euroset/65x20.png" alt="Евросеть" />
                        <img class="header-slider__logo" src="/images/logos/svyaznoy/65x20.png" alt="Связной" />
                    </a>
                <? endif ?>
            </div>
        </div>
    </div>
</div>