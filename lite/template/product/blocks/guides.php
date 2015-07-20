<?php
$f = function(
    $trustfactors = []
){
    // Будем считать, что инструкции - это все файловые трастфакторы
    /** @var $guides \Model\Product\Trustfactor[] */
    $guides = array_filter($trustfactors, function(\Model\Product\Trustfactor $t) {
        return $t->media && $t->media->isFile();
    });

    if (!$guides) return '';

    ?>

<div class="product-guides">
    <ul class="product-guides-list">
        <? foreach ($guides as $guide) : ?>
            <li class="product-guides-list__i">
                <a class="product-guides-list__lk" href="<?= $guide->media->getFileLink() ?>" target="_blank">
                    <i class="i-product i-product--pdf product-guides-list__icon"></i>
                    <span class="product-guides-list__lk-tl"><?= $guide->alt ?></span>
                </a>
            </li>
        <? endforeach ?>
    </ul>
</div>

<? }; return $f;