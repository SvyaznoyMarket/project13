<?php return function($value, $withPopup = true, $productUi = '', $propertyId = ''){
    if ($value && strpos($value, '<p') === false) {
        $value = "<p>$value</p>";
    }
?>

    <? if ($value): ?>
        <div class="props-list__hint js-product-properties-hint-container">
            <? if ($withPopup): ?>
                <a class="i-product i-product--hint js-product-properties-hint-opener" href=""></a>
                <?= \App::helper()->render('product-page/blocks/hint/popup', ['value' => $value]) ?>
            <? else: ?>
                <a class="i-product i-product--hint js-product-properties-hint-opener" href="" data-url="<?= \App::router()->generateUrl('ajax.product.property', [
                    'productUi' => $productUi,
                    'propertyId' => $propertyId,
                ]) ?>"></a>
            <? endif ?>
        </div>
    <? endif ?>

<? };