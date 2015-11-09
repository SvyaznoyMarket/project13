<?php
/**
 * @var $page                \View\User\OrderPage
 * @var $user                \Session\User
 * @var $recommendedProducts \Model\Product\Entity[]
 * @var $viewedProducts      \Model\Product\Entity[]
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="personalPage personal">

    <?= $page->render('user/_menu', ['page' => $page]) ?>


    <?= $helper->render(
        'product-page/blocks/slider',
        [
            'type'           => 'main',
            'title'          => 'Мы рекомендуем',
            'products'       => $recommendedProducts,
            'limit'          => \App::config()->product['itemsInSlider'],
            'page'           => 1,
            'class'          => '',
            'sender'   => [
                'name'     => 'retailrocket',
                'position' => 'UserRecommended',
                'method'   => 'PersonalRecommendation',
            ],
        ]
    ) ?>

    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => $viewedProducts,
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'class'     => 'slideItem-viewed',
        'isCompact' => true,
    ]) ?>

</div>