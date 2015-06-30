<?php

use Model\Product\Trustfactor;

$f = function(
     $trustfactors
){
  /** @var $trustfactors Trustfactor[] */
  /** @var $trusts Trustfactor[] */
  /** @var $clothSizeTrust Trustfactor */

    $trusts = array_filter($trustfactors, function(Trustfactor $t) { return $t->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD); });

    // Таблицу размеров одежды выводим
    $clothSizeTrust = array_filter($trustfactors, function(Trustfactor $t) { return $t->type == 'content' && strpos($t->name, 'Таблица размеров') !== false; });
    if ($clothSizeTrust) $clothSizeTrust = reset($clothSizeTrust);

?>

    <? if ($clothSizeTrust) : ?>
        <a class="jsImageInLightBox" href="<?= $clothSizeTrust->getImage() ?>" data-href="<?= $clothSizeTrust->getImage() ?>">Таблица размеров</a>
    <? endif ?>

    <!-- Трастфакторы -->
    <? if ($trustfactors) : ?>
    <ul class="product-card-assure">

        <? foreach ($trusts as $trust) : ?>

            <li class="product-card-assure__i">
                <div class="product-card-assure__l">
                    <img class="product-card-assure__img" src="<?= $trust->getImage() ?>">
                </div>
                <span class="product-card-assure__r"><?= $trust->alt ?></span>
            </li>

        <? endforeach ?>

    </ul>
    <? endif ?>
    <!-- /Трастфакторы -->

<? }; return $f;
