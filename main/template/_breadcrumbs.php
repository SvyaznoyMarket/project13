<?php
/**
 * @var $class       string|null
 * @var $breadcrumbs array('url' => null, 'name' => null)[]
 */

$hamburgerBreadcrumbsClass = \App::abTest()->isMenuHamburger() ? 'bBreadcrumbs--light' : '';
?>

<?php if ((bool)$breadcrumbs): ?>
<ul <?php if (isset($class) && !empty($class)): ?>class="bBreadcrumbs clearfix <?= $hamburgerBreadcrumbsClass ?>"<?php endif ?>>
    <? $i = 1; $count = count($breadcrumbs); foreach ($breadcrumbs as $breadcrumb): ?>
        <? if ($i < $count): ?>
            <? if(empty($breadcrumb['span'])) { ?>
              <li class="bBreadcrumbs__eItem"><a class="bBreadcrumbs__eLink" href="<?= $breadcrumb['url'] ?>"><?= $breadcrumb['name'] ?></a></li>
            <? } else { ?>
              <li class="bBreadcrumbs__eItem"><span><?= $breadcrumb['name'] ?></span></li>
            <? } ?>
        <? endif ?>
    <? $i++; endforeach ?>
</ul>
<? endif ?>
