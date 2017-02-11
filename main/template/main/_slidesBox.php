<?
/**
 * @var $page           \View\Main\IndexPage
 * @var $blockname      string
 * @var $class          string|null
 * @var $productList    \Model\Product\RichRelevanceProduct[]
 * @var $product        \Model\Product\RichRelevanceProduct
 * @var $rrProducts     array
 * @var $sender         array
 * @var $recommendationItem     \Model\Recommendation\RecommendationInterface
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

$rrProducts = array_filter($rrProducts, function($p){
    /** @var \Model\Product\Entity $p */
    return ($p instanceof \Model\Product\Entity) && $p->getIsBuyable() && !$p->isInShopShowroomOnly(); // SITE-5000
});

$blocks = array_chunk($rrProducts, 4);
$helper = new \Helper\TemplateHelper();
$i = 0;
?>

<div class="<?= $class ?> jsMainSlidesRetailRocket" data-block="<?= $recommendationItem->getPlacement() ?>">
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
                <? $clickTag = $product instanceof \Model\Product\RichRelevanceProduct ? $product->getOnClickTag() : '' ?>
                <? $productLink = $product->getLink() . '#' . http_build_query(['sender' => [
                    'name'      => $recommendationItem->getSenderName(),
                    'position'  => $recommendationItem->getPlacement(),
                    'from'      => 'MainPage'
                ]]) ?>
                <div class="item jsProductContainer" data-position="<?= $i ?>" data-ecommerce='<?= $product->ecommerceData() ?>'>
                    <a href="<?= $productLink ?>"
                       class="item_imgw" <?= $clickTag ?>
                    >
                        <img src="<?= $product->getMainImageUrl('product_160') ?>" class="item_img" alt="<?= $product->getName() ?>"/>
                    </a>
                    <div class="item_n"><a href="<?= $productLink ?>" <?= $clickTag ?>><?= $helper->escape($product->getName()) ?></a></div>
                    <div class="item_pr"><?= $helper->formatPrice($product->getPrice()) ?>&nbsp;<span class="rubl">p</span></div>
                    <?= $helper->render('cart/__button-product', [
                        'product'        => $product,
                        'sender'         => $sender,
                        'noUpdate'       => true,
                        'location'       => 'slider',
                        'useNewStyles'   => false
                    ]) // Кнопка купить ?>
                </div>
                <? $i++; endforeach ?>
            </li>
            <? endforeach ?>

        </ul>
    </div>
</div>