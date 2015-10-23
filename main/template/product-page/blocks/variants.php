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

    // Сохраняем URL-параметры
    $query = http_build_query(\App::request()->query->all());
    foreach ($product->model->property->option as $option) {
        $modelProduct = $option->product;
        $modelProduct->setLink($modelProduct->getLink() . '?'. $query);
    }

    ?>

    <div class="product-card-filter">
        <span class="product-card-filter__tl"><?= $product->model->property->name ?></span>

        <div class="product-card-filter__box">
            <div class="filter-btn-box filter-btn-box--bordered" onclick="$(this).toggleClass('filter-btn-box--open')">

                <div class="filter-btn-box__toggle">
                    <span class="filter-btn-box__tx"><?= $selectedModelOption ? \App::helper()->escape($selectedModelOption->value) : '' ?></span>
                    <i class="filter-btn-box__corner"></i>
                </div>

                <div class="filter-btn-box__dd" onmouseleave="$(this).parent().removeClass('filter-btn-box--open')">
                    <div class="filter-btn-box__inn" onscroll="(function(a){ var $this = $(a); $this.parent().addClass('scrolling'); setTimeout(function(){ $this.parent().removeClass('scrolling')}, 100)})(this)">
                        <ul class="filter-btn-box-lst">
                            <? foreach ($product->model->property->option as $option): ?>
                                <? if ($option->product): ?>
                                    <li class="filter-btn-box-lst__i"><a href="<?= $option->product->getLink() ?>" class="filter-btn-box-lst__lk"><?= $option->value ?></a></li>
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