<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $form             \View\Enterprize\Form
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $errors           array
 * @var $authSource       string|null
 * @var $products         \Model\Product\Entity[]|[]
 */
$products = !empty($products) && is_array($products) ? $products : [];
?>

<div class="titleForm">Мы отправим код на скидку в SMS и e-mail</div>

<? if (is_array($errors)): ?>
    <? foreach ($errors as $error): ?>
        <p class="red enterprizeWar"><?= $error ?></p>
    <? endforeach ?>
<? endif ?>

<?= $page->render('enterprize/form-registration',[
    'form'      => $form->setSubmit('Получить скидку >'),
    'authSource'=> $authSource,
])?>

<div class="epToggleRules">
    <?= $page->render('enterprize/_contentDescription') ?>
</div>

<?= $page->render('enterprize/_slider', ['enterpizeCoupon' => $enterpizeCoupon, 'products' => $products]) ?>
