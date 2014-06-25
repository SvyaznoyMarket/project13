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

<form class="formDefault jsEnterprizeForm" action="<?= $page->url('enterprize.form.update') ?>" method="post">
    <input type="hidden" name="user[guid]" value="<?= $form->getEnterprizeCoupon() ?>" />

    <fieldset class="formDefault__fields">
 
            <label class="formDefault__label">Имя:</label>
            <input class="formDefault__inputText jsName" type="text" name="user[name]" value="<?= $form->getName() ?>" />

            <label class="formDefault__label">Мобильный телефон:</label>
            <input class="formDefault__inputText jsMobile" type="text" name="user[mobile]" value="<?= $form->getMobile() ?>" <? if ('phone' === $authSource): ?>readonly="readonly"<? endif ?> />

            <label class="formDefault__label">E-mail:</label>
            <input class="formDefault__inputText jsEmail" type="text" name="user[email]" value="<?= $form->getEmail() ?>" <? if ('email' === $authSource): ?>readonly="readonly"<? endif ?> />
   

        <ul class="bInputList mEnterPrizeSubscr">
            <li class="bInputList__eListItem ">
                <input type="hidden" name="user[subscribe]" value="1" />
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsSubscribe" id="subscribe" type="checkbox" checked="checked" disabled="disabled" />
                <label class="bCustomLabel mCustomLabelBig mChecked" for="subscribe">Получить рекламную рассылку</label>
            </li>

            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsAgree" name="user[agree]" id="agree" type="checkbox" <? if($form->getAgree()): ?>checked="checked"<? endif ?> />
                <label class="bCustomLabel mCustomLabelBig<? if($form->getAgree()): ?> mChecked<? endif ?>" for="agree">Согласен с <a style="text-decoration: underline;" href="/reklamnaya-akcia-enterprize" target="_blank">условиями оферты</a></label>
            </li>
        </ul>

        <input class="formDefault__btnSubmit mBtnOrange" type="submit" value="Получить скидку >" />
    </fieldset>
</form>

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