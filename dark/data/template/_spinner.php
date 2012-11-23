<?php
/**
 * @var $page     \View\Layout
 * @var $incUrl   string       урл для увеличения количества
 * @var $decUrl   string       урл для уменьшения количества
 * @var $quantity int          количество
 */
?>

<div class="numerbox">
    <? if ($quantity > 1): ?>
        <a href="<?= $decUrl ?>"><b class="ajaless" title="Уменьшить"></b></a>
    <? else: ?>
        <b class="ajaless" title="Уменьшить"></b>
    <? endif ?>
    <input maxlength="2" class="ajaquant" value="<?= $quantity ?>" />
    <a href="<?= $incUrl ?>"><b class="ajamore" title="Увеличить"></b></a>
</div>