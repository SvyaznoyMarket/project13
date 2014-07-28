<?php

return function(
    \Helper\TemplateHelper $helper,
    array $slideData,
    $categoryToken = null
) { ?>

    <div
        id="promoCatalog"
        class="bPromoCatalog"
        data-slides="<?= $helper->json($slideData) ?>"
        data-use-interval="true"
        data-use-hash="false"
        data-use-carousel="true"
        data-category-token="<?= $categoryToken ?>"
        data-analytics-config="<?= $helper->json(\App::config()->tchiboSlider['analytics']) ?>" >

        <script type="text/html" id="slide_tmpl">
            <?= file_get_contents(__DIR__ . '/slide.tmpl'); ?>
        </script>

        <div class="bPromoCatalogSlider mTchiboSlider ">
            <a href="#" class="bPromoCatalogSlider_eArrow mArLeft"></a>
        <a href="#" class="bPromoCatalogSlider_eArrow mArRight"></a>
        <div class="bPromoCatalogSliderWrap jsPromoCatalogSliderWrap clearfix"></div>
        </div>

        <div id="promoCatalogPaginator" class="bPaginator mTchiboPaginator clearfix"></div>
    </div>
<? };