<?

use Model\Product\Trustfactor;

$f = function(
    \Model\Product\Entity $product,
    $trustfactors
){
    /** @var $trustfactors Trustfactor[] */

    if (!$product->model || !$product->model->property) return '';

    // Таблица размеров одежды
    $clothSizeTrust = array_filter($trustfactors, function(Trustfactor $t) { return $t->type == 'content' && strpos($t->name, 'Таблица размеров') !== false; });
    if ($clothSizeTrust) $clothSizeTrust = reset($clothSizeTrust);

    /** @var \Model\Product\Model\Property\Option\Entity|null $selectedModelOption */
    $selectedModelOption = call_user_func(function() use($product) {
        foreach ($product->model->property->option as $option) {
            if ($option->product && $option->product->ui === $product->ui) {
                return $option;
            }
        }

        return null;
    });
    ?>

    <div class="product-card-filter">
        <span class="product-card-filter__tl"><?= $product->model->property->name ?></span>

        <div class="product-card-filter__box">
            <div class="filter-btn-box filter-btn-box--bordered js-product-variations-dropbox-container">

                <div class="filter-btn-box__toggle js-product-variations-dropbox-opener">
                    <span class="filter-btn-box__tx"><?= $selectedModelOption ? \App::helper()->escape($selectedModelOption->value) : '' ?></span>
                    <i class="filter-btn-box__corner"></i>
                </div>

                <div class="filter-btn-box__dd js-product-variations-dropbox-content">
                    <div class="filter-btn-box__inn">
                        <ul class="filter-btn-box-lst">
                            <? foreach ($product->model->property->option as $option): ?>
                                <? if ($option->product): ?>
                                    <li class="filter-btn-box-lst__i js-product-variations-dropbox-item"><a href="<?= $option->product->getLink() ?>" class="filter-btn-box-lst__lk js-product-variations-dropbox-item-link"><?= $option->value ?></a></li>
                                <? endif ?>
                            <? endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <? if ($clothSizeTrust) : ?>
            <a class="product-card-filter__link i-product i-product--variant jsImageInLightBox" href="<?= $clothSizeTrust->getImage() ?>" data-href="<?= $clothSizeTrust->getImage() ?>">Таблица размеров</a>
        <? endif ?>

    </div>

<? }; return $f;