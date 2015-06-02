<?php
/**
 * @var $file \Model\Supplier\File
 */
?>
<li class="prices-list__i"  hint="<?= $file->name ?>">
    <i class="suppliers-load__icon"></i><span class="prices-list__file-name"><?= $file->name ?></span>
    <span class="prices-list__date"><?= $file->added->format('d.m.Y') ?></span>
</li>