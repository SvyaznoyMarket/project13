<?php
/** @var $page \View\DefaultLayout */
?>

<!-- добавляем, если добавили к сравнению товар topbarfix_cmpr-full -->
<div class="topbarfix_cmpr" data-bind="css: { 'topbarfix_cmpr-full': compare().length > 0 }">
    <a href="<?= \App::router()->generate('compare')?>" class="topbarfix_cmpr_tl">Сравнение</a>
    <span class="topbarfix_cmpr_qn" style="display: none" data-bind="visible: compare().length > 0 , text: compare().length"></span>
</div>