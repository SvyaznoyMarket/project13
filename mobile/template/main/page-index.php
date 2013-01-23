<?php
/**
 * @var $page    \View\Layout
 * @var $banners \Model\Banner\Entity[]
 */
?>

<div class="bWowAction">
    <div class="bMainBanner">
        <!-- блок главного баннера -->
    </div>
    <nav class="bContentMenu">
        <ul>
        <? foreach ($banners as $banner): ?>
            <li class="bContentMenu_eItem">
                <a class="bContentMenu_eLink mBlackLink" href="<?= $banner->getUrl() ?>"><?= $banner->getName() ?></a>
            </li>
        <? endforeach ?>
        </ul>
    </nav>
</div>