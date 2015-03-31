<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $user
) {
    /**
     * @var $user \Model\User\Entity|null
     */
    ?>

    <?= $helper->render('order-v3/lifegift/_header') ?>

    <section class="orderLgift">
        <div class="orderLgift_hd">
            <h1 class="orderLgift_t">ВЫ ОФОРМЛЯЕТЕ ЗАКАЗ В ПОДАРОК РЕБЕНКУ, КОТОРОГО ПОДДЕРЖИВАЕТ ФОНД "ПОДАРИ ЖИЗНЬ".</h1>
            <p class="orderLgift_slgn">ОПЛАТИТЕ ЗАКАЗ ОНЛАЙН, И ENTER ДОСТАВИТ ПОДАРОК РЕБЕНКУ К НОВОМУ ГОДУ.</p>
        </div>

        <form action="" class="orderU clearfix jsOrderForm" method="post">

            <fieldset class="orderU_flds">
                <legend class="orderLgift_st">Подарок</legend>

                <div class="orderLgift_prod">
                    <img class="orderLgift_img" src="<?= $product->getImageUrl(2) ?>" alt="<?= $product->getName() ?>">

                    <div class="orderLgift_dscr">
                        <div class="orderLgift_dscr_n"><?= $product->getPrefix() ?></div>
                        <div class="orderLgift_dscr_n"><?= $product->getWebName() ?></div>
                        <div class="orderLgift_dscr_pr"><?= $helper->formatPrice($product->getPrice()) ?>&nbsp;<span class="rubl">p</span></div>
                    </div>
                </div>
            </fieldset>

            <fieldset class="orderU_flds clearfix" style="margin-bottom: 0;">
                <legend class="orderLgift_st">От кого</legend>

                <div class="fl-l">
                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">Имя</label>
                        <input class="orderU_tx textfield" type="text" name="user_name" value="<?= $user ? $user->getName() : '' ?>" placeholder="">
                        <span class="orderU_hint">Как к вам обращаться?</span>
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                        <input class="orderU_tx textfield jsMobileField" type="text" name="user_phone" value="<?= $user ? $user->getMobilePhone() : '' ?>" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx">
                        <span class="orderU_hint">Для смс о состоянии заказа</span>

                        <div class="orderU_phones">Если вы делаете подарок из-за границы,<br/> укажите телефонный номер +7 (926) 529-42-01.</div>
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">E-mail</label>
                        <input class="orderU_tx textfield jsEmailField" type="text" name="user_mail" value="<?= $user ? $user->getEmail() : '' ?>" placeholder="">
                        <span class="orderU_hint">Для информации о заказе</span>
                    </div>
                </div>

                <? if (!$user) : ?>
                    <div class="orderAuth">
                        <div class="orderAuth_t">Уже заказывали у нас?</div>
                        <button class="orderAuth_btn btnLightGrey bAuthLink jsLoginButton">Войти с паролем</button>
                    </div>
                <? endif; ?>
            </fieldset>

            <fieldset class="orderU_flds orderU_flds-mb30">
                <div class="orderU_txbox">
                    <label for="" class="orderU_lbtx">Добрые пожелания ребёнку</label>
                    <textarea name="comment" id="" class="orderU_txarea"></textarea>
                </div>
            </fieldset>

            <fieldset class="orderU_flds clearfix">
                <legend class="orderLgift_st">Доступные способы оплаты</legend>

                <ul class="onpay_lst">
                    <li class="onpay_lst_i">
                        <input type="radio" id="pay1" value="card" name="paynow" class="jsCustomRadio customInput customInput-defradio" checked>
                        <label class="customLabel customLabel-defradio" for="pay1">
                            <span class="customLabel_tx">Банковская карта</span>
                            <img class="customLabel_img" src="/styles/order/img/icon_visa.png" alt="">
                            <img class="customLabel_img" src="/styles/order/img/icon_mc.png" alt="">
                        </label>
                        <p class="onpay_lst_desc">Visa, MasterCard</p>
                    </li>

                    <li class="onpay_lst_i">
                        <input type="radio" id="pay2" value="paypal" name="paynow" class="jsCustomRadio customInput customInput-defradio">
                        <label class="customLabel customLabel-defradio" for="pay2">
                            <span class="customLabel_tx">Платёжная система</span>
                            <img class="customLabel_img" src="/styles/order/img/icon_pp.png" alt="">
                        </label>
                    </li>
                </ul>

                <div class="orderLgift_paydscr">
                    <strong>Детям передаются только оплаченные товары. </strong>
                    <div class="onpay_footn">После нажатия кнопки "Оформить" вы будете перемещены на сайт платежной системы.</div>
                </div>
            </fieldset>

            <fieldset class="orderCompl clearfix">
                <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str">
                    <input type="checkbox" class="jsCustomRadio customInput customInput-checkbox jsAgreedCheckbox" id="accept" name="agreed" value="" />

                    <label  class="customLabel customLabel-checkbox" for="accept">
                        Адрес доставки:  <br/>
                        119048, г. Москва, ул. Доватора, д. 13, подъезд 2А, этаж 1 (вход с ул. 10-летия Октября).<br/>
                        Телефон/факс: 8-800-250-5222, +7 (495) 995-31-05.<br/>
                        Сотрудник фонда Юлия Сергеевна Сазонова. <br/>
                    </label>
                </div>

                <button type="submit" class="orderCompl_btn btnsubmit">Оформить</button>
            </fieldset>
        </form>
    </section>

<? } ?>