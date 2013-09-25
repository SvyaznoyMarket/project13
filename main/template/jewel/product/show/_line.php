<?php
/**
 * @var $page     \View\Layout
 * @var $product  \Model\Product\CompactEntity
 * @var $isHidden bool
 * @var $kit      \Model\Product\Kit\Entity
 * @var $addInfo  array
 * */
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$url = $page->url('product.line', array('lineToken' => $product->getLine()->getToken()));
$addInfo = isset($addInfo)?$addInfo:[];
?>

<div class="goodsbox height250"<? if ($isHidden): ?> style="display:none;"<? endif ?>>
    <div class="goodsbox__inner" data-url="<?= $product->getLink() ?>" <?php if (count($addInfo)) print 'data-add="'.$page->json($addInfo).'"'; ?>>
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
                <span class="font10 gray"> <? if ($product->getLine()->getTotalCount()): ?>(<?= $product->getLine()->getTotalCount() ?>)<? endif ?></span>
            </a>
        </h3>
    </div>
</div>