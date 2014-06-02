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
    <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как получить больше фишек?</span></p>

    <div class="enterPrizeListWrap">
        <ul class="enterPrizeList">
            <li class="enterPrizeList__item mBlue">
                <strong>Сайт www.enter.ru</strong><br>
                Всегда входите в личный кабинет.<br>
                Заказывайте товары как обычно.
            </li>

            <li class="enterPrizeList__item mOrange">
                <strong>Розничные магазины ENTER</strong><br>
                Входите в личный кабинет в терминале.<br>
                Заказывайте товары через терминал.
            </li>

            <li class="enterPrizeList__item mGreen">
                <strong>Контакт-сENTER 8 800 700 00 09</strong><br>
                Скажите оператору Контакт-cENTER, что Вы &mdash; участник Enter Prize!<br>
                Оператор поможет оформить заказ.
            </li>
        </ul>

        <div class="enterPrizeFinish">Ловите номер фишки в чеке после оплаты заказа!</div>
    </div>

    <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как играть фишками и получать скидки?</span></p>

    <div class="enterPrizeListWrap">
        <ul class="enterPrizeList">
            <li class="enterPrizeList__item mBlue">
                <strong>Сайт www.enter.ru</strong><br>
                Входите в личный кабинет на www.enter.ru!<br>
                При оформлении Заказа в поле КУПОН или ФИШКА вводите номер фишки! 
            </li>

            <li class="enterPrizeList__item mOrange">
                <strong>Розничные магазины ENTER</strong><br>
                Скажите сотруднику магазина, что Вы &mdash; участник Enter Prize!<br>
                И сообщите номер Фишки при оплате заказа! 
            </li>

            <li class="enterPrizeList__item mGreen">
                <strong>Контакт-сENTER 8 800 700 00 09</strong><br>
                Скажите оператору Контакт-cENTER, что Вы &mdash; участник Enter Prize!<br>
                И при оформлении заказа сообщите номер Фишки! 
            </li>
        </ul>
    </div>
</div>

<!--<div class="epSliderTitle">Фишка действует на товары</div>-->

<div class="epSliderBody">
    <!-- Код слайдера -->
    <? if (\App::config()->enterprize['showSlider']): ?>
        <?= $helper->render('product/__slider', [
            'type'     => 'enterprize',
            'title'    => 'Фишка действует на товары',
            'products' => $products,
            'count'    => null,
            'limit'    => \App::config()->enterprize['itemsInSlider'],
        ]) ?>
    <? endif ?>
</div>