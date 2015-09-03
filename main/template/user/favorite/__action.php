<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $containerId
 * @param \Model\Wishlist\Entity[] $wishlists
 * @param \Model\Wishlist\Entity|null $wishlist
 * @param array $actions
 */
$f = function (
    \Helper\TemplateHelper $helper,
    $containerId,
    array $wishlists,
    \Model\Wishlist\Entity $wishlist = null,
    $actions = []
) {
    $actions += [
        'share'  => true,
        'create' => true,
        'move'   => true,
        'delete' => true,
    ];
?>
    <div class="personal-favorit__choose">
        <input id="<?= ($containerId . '-selectAll') ?>" type="checkbox" class="personal-favorit__checkbox js-fav-all">
        <label for="<?= ($containerId . '-selectAll') ?>" class="personal-favorit__checkbox-icon"></label>
        <span class="choose-all">Выбрать все</span>
    </div>
    <ul class="personal-favorit__acts">
        <? if ($actions['share']): ?>
            <li
                class="personal-favorit__act js-favorite-shareProductPopup"
                data-container="<?= ('.' . $containerId) ?>"
                data-value="<?= $helper->json([]) ?>"
            >
                Поделиться
            </li>
        <? endif ?>
        <? if ($actions['create']): ?>
            <li
                class="personal-favorit__act js-favorite-createPopup"
                data-value="<?= $helper->json([
                    'form' => ['url' => $helper->url('wishlist.create')],
                ]) ?>"
            >
                Создать список
            </li>
        <? endif ?>
        <? if ($actions['move']): ?>
            <li
                class="personal-favorit__act js-favorite-movePopup"
                data-container="<?= ('.' . $containerId) ?>"
                data-value="<?= $helper->json([
                    'form'      => ['url' => $helper->url('wishlist.addProduct')],
                    'wishlists' => array_map(function(\Model\Wishlist\Entity $wishlist) { return ['id' => $wishlist->id, 'title' => $wishlist->title]; }, $wishlists),
                ]) ?>"
            >
                Перенести в список
            </li>
        <? endif ?>
        <? if ($actions['delete']): ?>
            <li
                class="personal-favorit__act js-favorite-deletePopup"
                data-container="<?= ('.' . $containerId) ?>"
                data-value="<?= $helper->json([
                    'wishlist' => $wishlist ? ['id' => $wishlist->id, 'title' => $wishlist->title] : null,
                    'form'     => [
                        'url' => $wishlist ? $helper->url('wishlist.deleteProduct') : $helper->url('favorite.deleteProducts')
                    ],
                ]) ?>"
            >
                Удалить
            </li>
        <? endif ?>
    </ul>
<? }; return $f;