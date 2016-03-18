<?php

use Model\Product\Trustfactor;

$f = function(
    $trustfactors
){
    /** @var $trustfactors Trustfactor[] */

    $trustfactors = array_filter($trustfactors, function(Trustfactor $t) { return $t->hasTag(Trustfactor::TAG_NEW_PRODUCT_CARD_PARTNER); });
    if (!$trustfactors) return null;
    ?>

        <!-- ссылки связной, сбер и многору -->
        <div class="product-discounts">
            <ul class="product-discounts-list">

            <? foreach ($trustfactors as $trust) : ?>
            <? if (('28f735f7-bb47-4d77-87d5-962557a2bd18' === $trust->uid) && !\App::config()->partners['MnogoRu']['enabled']) {
                continue;
            } ?>
                <li class="product-discounts-list__i">
                    <a class="product-discounts-list__lk" href="<?= $trust->link ?>" target="_blank">
                        <img src="<?= $trust->getImage() ?>">
                    </a>
                </li>
            <? endforeach ?>

            </ul>
        </div>
        <!--/ ссылки связной, сбер и многору -->

<? }; return $f;
