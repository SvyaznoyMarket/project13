<?
/**
 * @var $page           \View\Main\IndexPage
 * @var $blockname      string
 * @var $class          string|null
 * @var $productList    \Model\Product\Entity[]
 * @var $product        \Model\Product\Entity
 * @var $rrProducts     array
 */

// МЫ РЕКОМЕНДУЕМ

// фильтруем массив rr
foreach ($rrProducts as &$value) {
    if (@$productList[$value] instanceof \Model\Product\Entity) {
        $value = $productList[$value];
    } else {
        unset($value);
    }
} if (isset($value)) unset($value);

/* Сортируем блок "Популярные товары" */
if (false && @$blockname == 'ПОПУЛЯРНЫЕ ТОВАРЫ') {
    try {
        usort($rrProducts, function (\Model\Product\Entity $a, \Model\Product\Entity $b) {
            if ($b->getIsBuyable() != $a->getIsBuyable()) {
                return ($b->getIsBuyable() ? 1 : -1) - ($a->getIsBuyable() ? 1 : -1); // сначала те, которые можно купить
            } else if ($b->getPrice() != $a->getPrice()) {
                return $b->getPrice() - $a->getPrice();
            } else if ($b->isInShopOnly() != $a->isInShopOnly()) {
                //return ($b->isInShopOnly() ? -1 : 1) - ($a->isInShopOnly() ? -1 : 1); // потом те, которые можно зарезервировать
            } else if ($b->isInShopShowroomOnly() != $a->isInShopShowroomOnly()) {// потом те, которые есть на витрине
                return ($b->isInShopShowroomOnly() ? -1 : 1) - ($a->isInShopShowroomOnly() ? -1 : 1);
            } else {
                return (int)rand(-1, 1);
            }
        });
    } catch (\Exception $e) {}
}

if (@$blockname == 'МЫ РЕКОМЕНДУЕМ') $rrProducts = \Controller\Product\ProductHelperTrait::filterByModelId($rrProducts);

$rrProducts = array_filter($rrProducts, function($p){
    /** @var \Model\Product\Entity $p */
    return ($p instanceof \Model\Product\Entity) && $p->getIsBuyable() && !$p->isInShopShowroomOnly(); // SITE-5000
});

$blocks = array_chunk($rrProducts, 4);
$helper = new \Helper\TemplateHelper();

// открытие товаров в новом окне
$linkTarget = \App::abTest()->isNewWindow() ? ' target="_blank" ' : '';

?>

<div class="<?= $class ?> jsMainSlidesRetailRocket" data-block="<?= @$blockname == 'ПОПУЛЯРНЫЕ ТОВАРЫ' ? 'MainPopular' : 'MainRecommended' ?>">
    <div class="slidesBox_h">
        <div class="slidesBox_btn slidesBox_btn-l jsMainSlidesButton jsMainSlidesLeftButton"></div>

        <div class="slidesBox_h_c">
            <div class="slidesBox_t"><?= @$blockname ?></div>

            <ul class="slidesBox_dott">
                <? foreach ($blocks as $key => $block) : ?>
                <li class="slidesBox_dott_i <? if ($key == 0) : ?>slidesBox_dott_i-act<? endif ?>"></li>
                <? endforeach ?>
            </ul>
        </div>

        <div class="slidesBox_btn slidesBox_btn-r jsMainSlidesButton jsMainSlidesRightButton"></div>
    </div>

    <div class="slidesBox_inn">
        <ul class="slidesBox_lst clearfix jsMainSlidesProductBlock" data-count="<?= count($blocks) ?>">

            <? foreach ($blocks as $key => $block) : ?>
            <li class="slidesBox_i">
                <? foreach ($block as $product) : ?>
                <? if (!$product) continue ?>
                <? $productLink = $product->getLink() . '?' . http_build_query([
                    'sender[name]'      => 'retailrocket',
                    'sender[position]'  => @$blockname == 'ПОПУЛЯРНЫЕ ТОВАРЫ' ? 'MainPopular' : 'MainRecommended',
                    'sender[method]'    => @$blockname == 'ПОПУЛЯРНЫЕ ТОВАРЫ' ? 'ItemsToMain' : 'PersonalRecommendation',
                    'sender[from]'      => 'MainPage'
                ]) ?>
                <div class="item">
                    <a href="<?= $productLink ?>" class="item_imgw" <?= $linkTarget ?>><img src="<?= $product->getMainImageUrl('product_160') ?>" class="item_img" alt="<?= $product->getName() ?>"/></a>
                    <div class="item_n"><a href="<?= $productLink ?>" <?= $linkTarget ?>><?= $product->getName() ?></a></div>
                    <div class="item_pr"><?= $helper->formatPrice($product->getPrice()) ?>&nbsp;<span class="rubl">p</span></div>
                    <?= $helper->render('cart/__button-product', [
                        'product'        => $product,
                        'sender'         => $sender,
                        'noUpdate'       => true,
                        'location'       => 'slider',
                    ]) // Кнопка купить ?>
                    <!-- <a class="item_btn btn5" href="">Купить</a>-->
                </div>
                <? endforeach ?>
            </li>
            <? endforeach ?>

        </ul>
    </div>
</div>