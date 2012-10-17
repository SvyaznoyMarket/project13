<?php
/** @var $class string|null */
/** @var $breadcrumbs array('url' => null, 'name' => null)[] */
?>

<?php if ((bool)$breadcrumbs): ?>
<div <?php if (isset($class) && !empty($class)): ?>class="<?php echo $class ?>"<?php endif ?>>
    <a href="/">Enter.ru</a> &rsaquo;
    <? $i = 1; $count = count($breadcrumbs); foreach ($breadcrumbs as $breadcrumb): ?>
    <? if ($i < $count): ?>
        <a href="<?= $breadcrumb['url'] ?>"><?= $breadcrumb['name'] ?></a> &rsaquo;
        <? else: ?>
        <strong><?= $breadcrumb['name'] ?></strong>
        <? endif ?>
    <? $i++; endforeach ?>
</div>
<? endif ?>
