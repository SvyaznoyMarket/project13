<?php
/**
 * @var $page \View\Layout
 * @var $menu \Model\Menu\Entity[]
 */
?>

<? foreach ($menu as $item): ?>
    <a id="topmenu-root-<?= $item->getId() ?>" class="bToplink<?= (923 == $item->getId() && time() > strtotime('2013-01-25 00:00:00')) ? ' jew25' : '' ?>" title="<?= $item->getName() ?>" href="<?= $item->getLink() ?>"></a>
<? endforeach ?>
