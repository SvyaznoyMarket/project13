<?php
/**
 * @var $page                 \View\User\FavoritesPage
 * @var $helper               \Helper\TemplateHelper
 * @var $user                 \Session\User
 * @var $productsByUi         \Model\Product\Entity[]
 * @var $product              \Model\Product\Entity|null
 * @var $favoriteProductsByUi \Model\Favorite\Product\Entity[]
 * @var $wishlists            \Model\Wishlist\Entity[]
 */
?>

<div id="personal-container" class="personal">
    <?= $page->render('user/_menu-1508', ['page' => $page]) ?>

    <div class="personal__favorits id-favorite-container js-favorite-container disabled" data-disabled-class="disabled">
        <div class="personal-favorit__top">
            <?= $helper->render('user/favorite/__action', ['containerId' => 'id-favorite-container', 'wishlists' => $wishlists]) ?>
        </div>
        <? foreach ($favoriteProductsByUi as $favoriteProduct): ?>
        <?
            if (!$product = @$productsByUi[$favoriteProduct->ui]) continue;
            $rowId = sprintf('id-favoriteRow-%s', ($product->getUi() ?: uniqid()));
        ?>
            <?= $helper->render('user/favorite/__product', ['rowId' => $rowId, 'containerId' => 'id-favorite-container', 'product' => $product]) ?>
        <? endforeach ?>
    </div>


    <? foreach ($wishlists as $wishlist): ?>
    <?
        $containerId = sprintf('id-wishlist-container-%s', $wishlist->id ?: uniqid());
    ?>
    <div class="personal__favorits favorit-list<? if ($wishlist->products): ?> expanded <? else: ?> collapsed<? endif ?> js-favorite-container <?= $containerId ?> disabled" data-disabled-class="disabled">
        <div class="favorit-list__header">
            <ul class="personal-favorit__acts">
                <? if ($wishlist->products): ?>
                <li
                    class="personal-favorit__act js-favorite-shareFavoritePopup"
                    data-value="<?= $helper->json([
                        'wishlist' => ['id' => $wishlist->id, 'title' => $wishlist->title],
                    ]) ?>"
                    data-url="<?= 'http://www.enter.ru' . $helper->url('wishlist.show', ['wishlistToken' => $wishlist->token]) ?>"
                >Поделиться</li>
                <? endif ?>
                <li
                    class="personal-favorit__act js-favorite-deleteFavoritePopup"
                    data-value="<?= $helper->json([
                        'wishlist' => ['id' => $wishlist->id, 'title' => $wishlist->title],
                        'form'     => [
                            'url' => $helper->url('wishlist.delete'),
                        ],
                    ]) ?>"
                >Удалить</li>
            </ul>
            <div class="favorit-list__name js-toggle-list">Список: <?= $helper->escape($wishlist->title) ?></div>
        </div>
        <div class="personal-favorit__top">
            <?= $helper->render('user/favorite/__action', ['containerId' => $containerId, 'wishlists' => $wishlists, 'wishlist' => $wishlist, 'actions' => ['create' => false]]) ?>
        </div>
        <? foreach ($wishlist->products as $wishlistProduct): ?>
        <?
            if (!$product = @$productsByUi[$wishlistProduct->ui]) continue;
            $rowId = sprintf('id-wishlistRow-%s-%s', $wishlist->id, ($product->getUi() ?: uniqid()));
        ?>
            <?= $helper->render('user/favorite/__product', ['rowId' => $rowId, 'containerId' => $containerId, 'product' => $product]) ?>
        <? endforeach ?>
    </div>
    <? endforeach ?>

    <script id="tpl-favorite-messagePopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_message-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-createPopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_create-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-movePopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_move-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-deletePopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_delete-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-deleteFavoritePopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_deleteFavorite-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-shareProductPopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_shareProduct-popup.mustache') ?>
    </script>

    <script id="tpl-favorite-shareFavoritePopup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/user/favorite/_shareFavorite-popup.mustache') ?>
    </script>

</div>
