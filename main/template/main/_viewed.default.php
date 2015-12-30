<?php
/**
 * @var $page \View\Main\IndexPage
 */

if (\App::abTest()->getViewedOnMainCase() !== 1) {
    return;
}

?>

<!-- вы смотрели -->
<div class="product-section product-section--inn" style="margin-top: 40px;">
    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= \App::helper()->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended', ['productId' => null]),
            'sender'    => [
                'name'     => 'enter',
                'from'     => 'Main',
                'position' => 'Viewed_Main',
            ],
            'sender2' => [],
        ]) ?>
    <? endif ?>
</div>
<!--/ вы смотрели -->
