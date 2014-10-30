<?php
/**
 * @var $page     \View\Layout
 * @var $incUrl   string       урл для увеличения количества
 * @var $decUrl   string       урл для уменьшения количества
 * @var $quantity int          количество
 */
?>

<div class="numerbox">
    <a href="<?= $decUrl ?>" class="ajaxLess"><b class="ajaless" title="Уменьшить"></b></a>
    <input maxlength="2" class="ajaquant" value="<?= $quantity ?>" />
    <a href="<?= $incUrl ?>" class="ajaxMore"><b class="ajamore" title="Увеличить"></b></a>
</div>