<?php

use Model\Product\Trustfactor;

$f = function(
     $trustfactors
){
  /** @var $trustfactors Trustfactor[] */
  /** @var $trusts Trustfactor[] */

    $trusts = array_filter($trustfactors, function(Trustfactor $t) { return $t->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD); });

?>

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
