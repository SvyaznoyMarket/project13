<?php
/**
 * @var $page    \Mobile\View\DefaultLayout
 * @var $regions \Model\Region\Entity[]
 * @var $user    \Session\User
 */
?>

<header class="bHeader"><!-- хеадер страницы -->
    <div class="bHeaderFirstLine clearfix">
        <a class="bLogo mHiddenText mFl" href="/">Enter</a>

        <div class="bSelectRegion mFl">
            <select class="bSelectRegion_eSelect" size="1">
            <? foreach ($regions as $region): ?>
                <option value="<?= $page->url('region.change', array('regionId' => $region->getId())) ?>" class="bSelectRegion_eRegion"<? if ($user->getRegion()->getId() == $region->getId()): ?> selected="selected"<? endif ?>><?= $region->getName() ?></option>
            <? endforeach ?>
            </select>
        </div>
        <a class="bCatalogLink mFr grayButton mEnter" href="<?= $page->url('product.category') ?>">Каталог</a>
    </div>
    <div class="bHeaderSecondLine clearfix">
        <div class="bSearch mFl">
            <form>
                <input class="bSearch_eText mFl" type="text">
                <input class="bSearch_eButton mFl" type="submit" value="">
            </form>
        </div>
        <a class="bHeaderSecondLine_eLink mFr" href="<?= $page->url('cart') ?>">Корзина</a>
        <a class="bHeaderSecondLine_eLink mFr" href="<?= $page->url('user.login') ?>">Вход</a>
    </div>
</header>
