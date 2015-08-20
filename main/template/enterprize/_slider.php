<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $products         \Model\Product\Entity[]|[]
 */
$helper = new \Helper\TemplateHelper();
?>

<div class="epSliderBody">
    <!-- Код слайдера -->
    <? if (\App::config()->enterprize['showSlider']): ?><?
        $sliderTitle = 'Фишка действует на товары';
        if (($enterpizeCoupon->getLinkName() || $enterpizeCoupon->getName()) && $enterpizeCoupon->getLink()) {
            $linkName = $enterpizeCoupon->getLinkName() ?: $enterpizeCoupon->getName();
            $link = '<strong><a target="_blank" style="text-decoration: underline;" href="'.$enterpizeCoupon->getLink().'">'.$linkName.'</a></strong>';
            $sliderTitle = "Фишка действует на все товары из раздела $link, например:";
        } ?>

        <?= $helper->render('product/__slider', [
            'type'     => 'enterprize',
            'title'    => $sliderTitle,
            'products' => $products,
            'limit'    => \App::config()->enterprize['itemsInSlider'],
        ]) ?>
    <? endif ?>
</div>
 