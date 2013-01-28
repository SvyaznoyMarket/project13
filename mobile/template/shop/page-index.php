<?php
/**
 * @var $page   \View\Layout
 * @var $region \Model\Region\Entity
 * @var $shops  \Model\Shop\Entity[]
 */
?>

<a class="bMenuBack mBlackLink" href="<?= $page->url('homepage') ?>">Главная</a>
<nav class="bContentMenu">
    <ul>
    <? foreach ($shops as $shop): ?>
        <li class="bContentMenu_eItem">
            <a class="bContentMenu_eLink mBlackLink" href="<?= $page->url('shop.show', ['regionToken' => $region->getToken(), 'shopToken' => $shop->getToken()]) ?>">
                <?= $shop->getName() ?>
            </a>
        </li>
    <? endforeach ?>
    </ul>
</nav>