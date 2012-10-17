<?php
/**
 * @var $page     \View\DefaultLayout
 * @var $product  \Model\Product\CompactEntity
 * @var $isHidden bool
 * @var $kit      \Model\Product\Kit\Entity
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$url = $page->url('product.line', array('lineId' => $product->getLine()->getId()));
?>

<div class="goodsbox height250"<? if ($isHidden): ?> style="display:none;"<? endif ?>>
    <div class="photo">
        <a href="<?= $url ?>">
            <? if ($label = $product->getLabel()): ?>
                <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
            <? endif ?>
            <img src="<?= $product->getImageUrl() ?>" alt="Серия <?= $product->getLine()->getName() ?>" title="Серия <?= $product->getLine()->getName() ?>" width="160" height="160"/>
        </a>
    </div>
    <h3>
        <a href="<?= $url ?>">
            <strong>Серия <?= $product->getLine()->getName() ?></strong>
            <span class="font10 gray"> (<?= $product->getLine()->getTotalCount() ?>)</span>
        </a>
    </h3>

    <!-- Hover -->
    <div class="boxhover"<? if ($product->getIsBuyable()): ?> ref="<?= $product->getToken() ?>"<? endif ?>>
        <b class="rt"></b><b class="lb"></b>

        <div class="rb">
            <div class="lt" data-url="<?= $url ?>">
                <div class="photo">
                    <a href="<?= $url ?>">
                        <? if ($label = $product->getLabel()): ?>
                            <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
                        <? endif ?>
                        <img src="<?= $product->getImageUrl() ?>" alt="Серия <?= $product->getLine()->getName() ?>" title="Серия <?= $product->getLine()->getName() ?>" width="160" height="160"/>
                    </a>
                </div>
                <h3>
                    <a href="<?= $url ?>">
                        <strong>Серия <?= $product->getLine()->getName() ?></strong>
                        <span class="font10 gray"> (<?= $product->getLine()->getTotalCount() ?>)</span>
                    </a>
                </h3>
            </div>
        </div>
    </div>
    <!-- /Hover -->

</div>