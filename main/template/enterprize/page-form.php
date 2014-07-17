<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $form             \View\Enterprize\Form
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $errors           array
 * @var $authSource       string|null
 * @var $products         \Model\Product\Entity[]
 */
$products = !empty($products) && is_array($products) ? $products : [];
$helper = new \Helper\TemplateHelper();
?>

<div class="titleForm">Мы отправим код на скидку в SMS и e-mail</div>

<? if (is_array($errors)): ?>
    <? foreach ($errors as $error): ?>
        <p class="red enterprizeWar"><?= $error ?></p>
    <? endforeach ?>
<? endif ?>

<?=$page->render('enterprize/form-registration',[
    'form'      => $form,
    'authSource'=> $authSource,
])?>

<div class="epToggleRules">
    <?= $page->render('enterprize/_contentDescription') ?>
</div>

<!--<div class="epSliderTitle">Фишка действует на товары</div>-->

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
            'count'    => null,
            'limit'    => \App::config()->enterprize['itemsInSlider'],
        ]) ?>
    <? endif ?>
</div>