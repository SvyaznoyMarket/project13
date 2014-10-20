<?php
/**
 * @var $id     int
 * @var $typeId int
 */
?>

<!-- кнопка сравнения -->
<div class="btnCmpr"
     data-bind="compareButtonBinding: compare"
     data-id="<?= $id ?>"
     data-type-id="<?= $typeId ?>">

    <a class="btnCmpr_lk jsCompareLink" href="<?= \App::router()->generate('compare.add', ['productId' => $id]) ?>">
        <span class="btnCmpr_tx">Добавить к сравнению</span>
    </a>

    <!-- если в сравнении есть несколько товаров из одной категории -->
    <div class="btnCmpr_more" style="display: none">
        <a class="btnCmpr_more_lk" href="<?= \App::router()->generate('compare', ['typeId' => $typeId]) ?>">Сравнить</a> <span class="btnCmpr_more_qn"></span>
    </div>
</div>