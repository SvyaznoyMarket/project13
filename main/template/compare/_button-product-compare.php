<?php
/**
 * @var $id             int
 * @var $categoryId     int
 */
?>

<!-- кнопка сравнения -->
<div class="btnCmpr"
     data-bind="compareButtonBinding: compare"
     data-id="<?= $id ?>"
     data-category-id="<?= $categoryId ?>"
     data-delete-url="<?= \App::router()->generate('compare.delete', ['id' => $id]) ?>"
     data-add-url="<?= \App::router()->generate('compare.add', ['id' => $id]) ?>">

    <a class="btnCmpr_lk jsCompareLink" href="<?= \App::router()->generate('compare.add', ['id' => $id]) ?>">
        <span class="btnCmpr_tx">Добавить к сравнению</span>
    </a>

    <!-- если в сравнении есть несколько товаров из одной категории -->
    <div class="btnCmpr_more" style="display: none">
        <a class="btnCmpr_more_lk" href="<?= \App::router()->generate('compare') ?>">Сравнить</a> <span class="btnCmpr_more_qn"></span>
    </div>

</div>
