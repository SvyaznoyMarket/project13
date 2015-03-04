<?php
/**
 * @var $page     \View\User\OrderPage
 * @var $user     \Session\User
 * @var $products \Model\Product\Entity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="personalPage">

<?= $page->render('user/_menu', ['page' => $page]) ?>

<!--<div class="personalTitle">Товары для вас <span class="personalTitle_count"><?= count($products) ?></span></div>-->

    <?= $helper->render('product/__slider', [
        'type'           => 'main',
        'title'          => 'Мы рекомендуем',
        'products'       => array_values($products),
        'count'          => count($products),
        'limit'          => \App::config()->product['itemsInSlider'],
        'page'           => 1,
        'class'          => 'slideItem-7item',
        'sender'   => [
            'name'     => 'retailrocket',
            'position' => 'UserRecommended',
            'method'   => 'PersonalRecommendation',
        ],
    ]) ?>

</div>