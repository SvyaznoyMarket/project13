<?php
/**
 * @var \Model\Product\Entity $product
 */

$id = $product->getId();
$typeId = $product->getType() ? $product->getType()->getId() : null;
?>

<!-- кнопка сравнения -->
<div class="btnCmpr"
     data-bind="compareButtonBinding: compare"
     data-id="<?= $id ?>"
     data-type-id="<?= $typeId ?>">

    <a id="<?= 'compareButton-' . $id ?>" class="btnCmpr_lk jsCompareLink" href="<?= \App::router()->generate('compare.add', ['productId' => $id, 'location' => 'product']) ?>" data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>" data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>">
        <!--noindex--><span class="btnCmpr_tx">Добавить к сравнению</span><!--/noindex-->
    </a>

    <!-- если в сравнении есть несколько товаров из одной категории -->
    <div class="btnCmpr_more" style="display: none">
        <a class="btnCmpr_more_lk" href="<?= \App::router()->generate('compare', ['typeId' => $typeId]) ?>">Сравнить</a> <span class="btnCmpr_more_qn"></span>
    </div>
</div>