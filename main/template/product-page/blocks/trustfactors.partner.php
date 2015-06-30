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

                <li class="product-discounts-list__i">
                    <a class="product-discounts-list__lk" href="<?= $trust->link ?>">
                        <img src="<?= $trust->getImage() ?>">
                    </a>
                </li>

            <? endforeach ?>

            </ul>
        </div>
        <!--/ ссылки связной, сбер и многору -->

<? }; return $f;
