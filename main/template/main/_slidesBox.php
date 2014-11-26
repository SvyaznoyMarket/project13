<?
/**
 * @var $page           \View\Main\IndexPage
 * @var $class          string|null
 * @var $productList    \Model\Product\BasicEntity[]
 * @var $product        \Model\Product\BasicEntity
 * @var $rrProducts     array
 */

// МЫ РЕКОМЕНДУЕМ

// фильтруем массив rr
foreach ($rrProducts as &$value) {
    if (@$productList[$value] instanceof \Model\Product\BasicEntity) {
        $value = $productList[$value];
    } else {
        unset($value);
    }
}

$blocks = array_chunk($rrProducts, 4);
$helper = new \Helper\TemplateHelper();

?>

<div class="<?= $class ?> jsMainSlidesRetailRocket">
    <div class="slidesBox_h">
        <div class="slidesBox_btn slidesBox_btn-l jsMainSlidesButton jsMainSlidesLeftButton"></div>

        <div class="slidesBox_h_c">
            <div class="slidesBox_t">ПОПУЛЯРНЫЕ ТОВАРЫ</div>

            <ul class="slidesBox_dott">
                <? foreach ($blocks as $key => $block) : ?>
                <li class="slidesBox_dott_i <? if ($key == 0) : ?>slidesBox_dott_i-act<? endif ?>"></li>
                <? endforeach ?>
            </ul>
        </div>

        <div class="slidesBox_btn slidesBox_btn-r jsMainSlidesButton jsMainSlidesRightButton"></div>
    </div>

    <div class="slidesBox_inn">
        <ul class="slidesBox_lst clearfix jsMainSlidesProductBlock">

            <? foreach ($blocks as $key => $block) : ?>
            <li class="slidesBox_i">
                <? foreach ($block as $product) : ?>
                <div class="item">
                    <a href="<?= $product->getLink() ?>" class="item_imgw"><img src="<?= $product->getImageUrl() ?>" class="item_img" alt="<?= $product->getName() ?>"/></a>
                    <div class="item_n"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
                    <div class="item_pr"><?= $helper->formatPrice($product->getPrice()) ?>&nbsp;<span class="rubl">p</span></div>
                    <a class="item_btn btn5" href="">Купить</a>
                </div>
                <? endforeach ?>
            </li>
            <? endforeach ?>

        </ul>
    </div>
</div>