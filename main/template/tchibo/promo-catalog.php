<?php

return function(
    \Helper\TemplateHelper $helper,
    array $slideData
) { ?>

    <div id="promoCatalog" class="bPromoCatalog" data-slides="<?= $helper->json($slideData) ?>" data-use-interval="true" data-use-hash="false" data-use-carousel="true" data-use-tchibo-analytics="<?= (bool)\App::config()->tchiboSlider['analyticsEnabled'] ?>">

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