<?php

use Model\Product\Trustfactor;

$f = function(
     $trustfactors
){
  /** @var $trustfactors Trustfactor[] */

  $trustfactors = array_filter($trustfactors, function(Trustfactor $t) { return $t->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD); });
  if (!$trustfactors) return null;
?>

    <ul class="product-card-assure">

        <? foreach ($trustfactors as $trust) : ?>

            <li class="product-card-assure__i">
                <div class="product-card-assure__l">
                    <img class="product-card-assure__img" src="<?= $trust->getImage() ?>">
                </div>
                <span class="product-card-assure__r"><?= $trust->alt ?></span>
            </li>

        <? endforeach ?>

    </ul>

<? }; return $f;
