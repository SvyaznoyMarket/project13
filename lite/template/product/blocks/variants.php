<?

use Model\Product\Trustfactor;

$f = function(
    \Model\Product\Entity $product,
    $trustfactors
){
    /** @var $trustfactors Trustfactor[] */

    if (!$product->getModel() || !$product->getModel()->getProperty()) return '';

    // Таблица размеров одежды
    $clothSizeTrust = array_filter($trustfactors, function(Trustfactor $t) { return $t->type == 'content' && strpos($t->name, 'Таблица размеров') !== false; });
    if ($clothSizeTrust) $clothSizeTrust = reset($clothSizeTrust);

    ?>

    <div class="product-card-filter">

        <? foreach ($product->getModel()->getProperty() as $property) : ?>

        <span class="product-card-filter__tl"><?= $property->getName() ?></span>

        <div class="product-card-filter__box">
            <div class="filter-btn-box filter-btn-box--bordered" onclick="$(this).toggleClass('filter-btn-box--open')">

                <div class="filter-btn-box__toggle">
                    <span class="filter-btn-box__tx"><?= $product->getPropertyById($property->getId())->getStringValue() ?></span>
                    <i class="filter-btn-box__corner"></i>
                </div>

                <div class="filter-btn-box__dd" onmouseleave="$(this).parent().removeClass('filter-btn-box--open')">
                    <div class="filter-btn-box__inn" onscroll="(function(a){ var $this = $(a); $this.parent().addClass('scrolling'); setTimeout(function(){ $this.parent().removeClass('scrolling')}, 100)})(this)">
                        <ul class="filter-btn-box-lst">
                            <? foreach ($property->getOption() as $option): ?>
                                <li class="filter-btn-box-lst__i"><a href="<?= $option->getProduct()->getLink() ?>" class="filter-btn-box-lst__lk"><?= $option->getHumanizedName() ?></a></li>
                            <? endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <? endforeach ?>

        <? if ($clothSizeTrust) : ?>
            <a class="product-card-filter__link jsImageInLightBox" href="<?= $clothSizeTrust->getImage() ?>" data-href="<?= $clothSizeTrust->getImage() ?>">Таблица размеров</a>
        <? endif ?>

    </div>

<? }; return $f;