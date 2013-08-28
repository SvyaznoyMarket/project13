<?php
/** @var $page \View\User\CorporateRegistrationPage */
/** @var $form \View\User\CorporateRegistrationForm */
?>

<? /**<form action="<?= $page->url('user.registerCorporate') ?>" method="post">

    <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

    <div class="pb5">Имя:</div>
    <div class="pb5">
        <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[first_name]" value="<?= $form->getFirstName() ?>" />
    </div>

    <div class="pb5">Отчество:</div>
    <div class="pb5">
        <? if ($error = $form->getError('middle_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[middle_name]" value="<?= $form->getMiddleName() ?>" />
    </div>

    <div class="pb5">Фамилия:</div>
    <div class="pb5">
        <? if ($error = $form->getError('last_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[last_name]" value="<?= $form->getLastName() ?>" />
    </div>

    <div class="pb5">Контактный e-mail:</div>
    <div class="pb15">
        <? if ($error = $form->getError('email')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[email]" value="<?= $form->getEmail() ?>" /><br />
        <label class="bSubscibe checked">
            <b></b> Хочу знать об интересных<br />предложениях
            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="subscibe" checked="checked" />
        </label>
    </div>

    <div class="pb5">Мобильный телефон:</div>
    <div class="pb5">
        <? if ($error = $form->getError('phone')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[phone]" value="<?= $form->getPhone() ?>" maxlength="11" />
        <i>Например, 89081234567</i>
    </div>

    <div class="pb5">Организационно правовая форма:</div>
    
    <div class="pb5">
        <select id="corp_select" name="register[corp_form]" class="text width315 mb10">
        <? foreach ($form->getCorpFormSelection() as $value => $name): ?>
            <option value="<?= $value ?>"<? if ($value == $form->getCorpForm()): ?> selected="selected" <? endif ?>><?= $name ?></option>
        <? endforeach ?>
            <option value="Другая форма">Другая форма</option>
        </select>
    </div>

    <div class="pb5">Наименование организации:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_name')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_name]" value="<?= $form->getCorpName() ?>" />
    </div>

    <div class="pb5">Юридический адрес:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_legal_address')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_legal_address]" value="<?= $form->getCorpLegalAddress() ?>" />
    </div>

    <div class="pb5">Фактический адрес:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_real_address')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_real_address]" value="<?= $form->getCorpRealAddress() ?>" />
    </div>

    <div class="pb5">ИНН:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_inn')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_inn]" value="<?= $form->getCorpINN() ?>" />
    </div>

    <div class="pb5">КПП:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_kpp')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_kpp]" value="<?= $form->getCorpKPP() ?>" />
    </div>

    <div class="pb5">Расчетный счет:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_account')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_account]" value="<?= $form->getCorpAccount() ?>" />
    </div>

    <div class="pb5">Корреспондентский счет:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_korr_account')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_korr_acount]" value="<?= $form->getCorpKorrAccount() ?>" />
    </div>

    <div class="pb5">БИК:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_bik')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_bik]" value="<?= $form->getCorpBIK() ?>" />
    </div>

    <div class="pb5">Код ОКПО:</div>
    <div class="pb5">
        <? if ($error = $form->getError('corp_okpo')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_okpo]" value="<?= $form->getCorpOKPO() ?>" />
    </div>

    <? if (false): ?>
    <div class="pb5">Код ОКВЭД:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_okved')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_okved]" />
    </div>

    <div class="pb5">E-mail:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_email')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_email]" />
    </div>

    <div class="pb5">Телефон:</div>
    <div class="pb5">
        <? //if ($error = $form->getError('corp_phone')) echo $page->render('_formError', array('error' => $error)) ?>
        <input type="text" class="text width315 mb10" name="register[corp_phone]" />
    </div>
    <? endif ?>

    <input type="submit" tabindex="4" value="Регистрация" class="button bigbutton" />

</form>
<div id="corpNotice" class="popup width315">
    <a class="close" href="#">Закрыть</a>
    <p class="font16">Для дальнейшей регистрации на нашем сайте просим выслать карточку основных сведений организации по адресу <a href="mailto:partner@enter.ru">partner@enter.ru</a></p>
    <p class="font16">Мы свяжемся с вами в течение 10 минут.</p>
</div>

**/ ?>

<div class="bBusinessReg">
    <div class="bHead clearfix">
        <h1 class="bHead__eTitle">Enter Business*</h1>

        <ul class="bHeadRegLink">
            <li class="bHeadRegLink__eItem"><a data-goto="bCorpRegFormHead" class="bHeadRegLink__eLink jsGoToId" href="">Регистрация</a></li>
            <li class="bHeadRegLink__eItem"><a class="bHeadRegLink__eLink" href="">Вход в личный кабинет</a></li>
        </ul>
    </div>


    <div class="bCont clearfix">
        <div class="bCont__eLeft">
            <h2 class="bTitle">Enter – это новый способ покупать!<br/>Почему?</h2>

            <ul class="bBusinessRegList">
                <li class="bBusinessRegList__eItem"><span class="bText">Уже более 3500 компаний выбрали нас и стали нашими постоянными клиентами!</span></li>

                <li class="bBusinessRegList__eItem"><span class="bText">Заказать товар можно любым удобным способом: <br/></span>

                    <ul class="bBusinessRegSubList">
                        <li class="bBusinessRegSubList__eItem"><span class="bText">на сайте;</span></li>
                        <li class="bBusinessRegSubList__eItem"><span class="bText">через круглосуточный Контакт-cENTER<br/><strong>8 (800) 700-00-09</strong> (звонок бесплатный);</span></li>
                        <li class="bBusinessRegSubList__eItem"><span class="bText">а также при помощи персонального менеджера по прямому номеру <strong>+7 (495) 775-78-85</strong> или по e-mail по email: <strong><a class="bMail" href="mailto:partner@enter.ru">partner@enter.ru</a></strong> или в магазинах Enter.</span></li>
                    </ul>
                </li>

                <li class="bBusinessRegList__eItem"><span class="bText">Мы поможем сориентироваться в нашем ассортименте<br/>и выставим счет в ваш личный кабинет в течение 30 минут.</span></li>

                <li class="bBusinessRegList__eItem"><span class="bText">Десятки тысяч товаров в 15 категориях:<br/>
                <a href="/catalog/furniture">мебель</a>, <a href="/catalog/appliances">бытовая техника</a>, <a href="/catalog/electronics">электроника</a>, <a href="/catalog/children">детские товары</a>,<br/><a href="/catalog/household">товары для дома</a>, <a href="/catalog/do_it_yourself/tovari-dlya-sada-311">сад и огород</a>, <a href="/catalog/gift_hobby">подарки и хобби</a>, <a href="/catalog/do_it_yourself">сделай сам</a>, <a href="/catalog/sport">спорт и отдых</a>, <a href="/catalog/do_it_yourself/aksessuari-dlya-avtomobiley-225">аксессуары для автомобилей</a>, <a href="/catalog/parfyumeriya-i-kosmetika">парфюмерия и косметика</a>, <a href="/catalog/jewel">ювелирные украшения и часы</a>, <a href="/catalog/electronics/muzikalnie-instrumenti-2396">музыкальные инструменты</a>, <a href="/catalog/tovari-dlya-givotnih">зоотовары</a>, <a href="/catalog/appliances/krasota-i-zdorove-21">красота и здоровье</a>,
                a так же подарочные карты для сотрудников и партнеров вашей компании.</span></li>

                <li class="bBusinessRegList__eItem"><span class="bText">WOW-ЦЕНЫ и бесплатная доставка на первый заказ.</span></li>
            </ul>

            <ul class="bDownList">
                <li class="bDownList__eItem mPdf"><a class="bDownList__eLink" href="http://content.enter.ru/wp-content/uploads/2013/08/Презентация-для-юр.лиц_август-2013_Russian.pdf">Скачать презентацию</a> 2,4 Мб</li>
                <li class="bDownList__eItem"><a class="bDownList__eLink" href="">Как начать сотрудничество?</a></li>
            </ul>

            <h2 class="bTitle" id="bCorpRegFormHead">Регистрация юридического лица</h2>

            <form class="bCorpRegForm" action="<?= $page->url('user.registerCorporate') ?>" method="post">

                <? if ($error = $form->getError('global')) echo $page->render('_formError', array('error' => $error)) ?>

                <label class="bCorpRegForm__eLabel">Имя:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[first_name]" value="<?= $form->getFirstName() ?>" />
                <? if ($error = $form->getError('first_name')) echo $page->render('_formError', array('error' => $error)) ?>

                <label class="bCorpRegForm__eLabel">Отчество:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[middle_name]" value="<?= $form->getMiddleName() ?>" />
                <? if ($error = $form->getError('middle_name')) echo $page->render('_formError', array('error' => $error)) ?>

                <label class="bCorpRegForm__eLabel">Фамилия:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[last_name]" value="<?= $form->getLastName() ?>" />
                <? if ($error = $form->getError('last_name')) echo $page->render('_formError', array('error' => $error)) ?>

                <label class="bCorpRegForm__eLabel">E-mail:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[email]" value="<?= $form->getEmail() ?>" />
                <? if ($error = $form->getError('email')) echo $page->render('_formError', array('error' => $error)) ?>

                <label class="bCorpRegForm__eLabel">Мобильный телефон:</label>
                <input type="text" class="bCorpRegForm__eInput" name="register[phone]" value="<?= $form->getPhone() ?>" maxlength="11" placeholder="89ХХХХХХХХХ" />
                <? if ($error = $form->getError('phone')) echo $page->render('_formError', array('error' => $error)) ?>

                <div class="bInputList">
                    <input type="checkbox" id="subscribeCheck" name="subscribe" value="1" autocomplete="off" class="bCustomInput mCustomCheckbox" checked="checked" hidden />
                    <label class="bCustomLabel" for="subscribeCheck">Хочу знать об интересных предложениях</label>
                </div>

                <div class="bCompanyData">
                <div class="bCompanyDataLink mClose"><span class="bCompanyDataLink__eText">Указать реквизиты:</span></div>

                <div class="bCompanyDataSection" style="display: none;">
                    <label class="bCorpRegForm__eLabel m2Line">Организационно правовая форма:</label>
                    <select id="corp_select" name="register[corp_form]" class="bCorpRegForm__eSelect">
                    <? foreach ($form->getCorpFormSelection() as $value => $name): ?>
                        <option value="<?= $value ?>"<? if ($value == $form->getCorpForm()): ?> selected="selected" <? endif ?>><?= $name ?></option>
                    <? endforeach ?>
                        <option value="Другая форма">Другая форма</option>
                    </select>

                    <label class="bCorpRegForm__eLabel m2Line">Наименование организации:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_name]" value="<?= $form->getCorpName() ?>" />
                    <? if ($error = $form->getError('corp_name')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">Юридический адрес:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_legal_address]" value="<?= $form->getCorpLegalAddress() ?>" />
                    <? if ($error = $form->getError('corp_legal_address')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">Фактический адрес:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_real_address]" value="<?= $form->getCorpRealAddress() ?>" />
                    <? if ($error = $form->getError('corp_real_address')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">ИНН:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_inn]" value="<?= $form->getCorpINN() ?>" />
                    <? if ($error = $form->getError('corp_inn')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">КПП:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_kpp]" value="<?= $form->getCorpKPP() ?>" />
                    <? if ($error = $form->getError('corp_kpp')) echo $page->render('_formError', array('error' => $error)) ?>


                    <label class="bCorpRegForm__eLabel">Расчетный счет:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_account]" value="<?= $form->getCorpAccount() ?>" />
                    <? if ($error = $form->getError('corp_account')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel m2Line">Корреспондентский счет:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_korr_acount]" value="<?= $form->getCorpKorrAccount() ?>" />
                    <? if ($error = $form->getError('corp_korr_account')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">БИК:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_bik]" value="<?= $form->getCorpBIK() ?>" />
                    <? if ($error = $form->getError('corp_bik')) echo $page->render('_formError', array('error' => $error)) ?>

                    <label class="bCorpRegForm__eLabel">Код ОКПО:</label>
                    <input type="text" class="bCorpRegForm__eInput" name="register[corp_okpo]" value="<?= $form->getCorpOKPO() ?>" />
                    <? if ($error = $form->getError('corp_okpo')) echo $page->render('_formError', array('error' => $error)) ?>

                    <? if (false): ?>
                        <label class="bCorpRegForm__eLabel">Код ОКВЭД:</label>
                        <? //if ($error = $form->getError('corp_okved')) echo $page->render('_formError', array('error' => $error)) ?>
                        <input type="text" class="bCorpRegForm__eInput" name="register[corp_okved]" />

                        <label class="bCorpRegForm__eLabel">E-mail:</label>
                        <? //if ($error = $form->getError('corp_email')) echo $page->render('_formError', array('error' => $error)) ?>
                        <input type="text" class="bCorpRegForm__eInput" name="register[corp_email]" />

                        <label class="bCorpRegForm__eLabel">Телефон:</label>
                        <? //if ($error = $form->getError('corp_phone')) echo $page->render('_formError', array('error' => $error)) ?>
                        <input type="text" class="bCorpRegForm__eInput" name="register[corp_phone]" />
                    <? endif ?>
                </div>
            </div>

                <input type="submit" tabindex="4" value="Регистрация" class="button bigbutton" />

                <div class="bFootenoteBussines">* Энтер Бизнес</div>
            </form>

            <div id="corpNotice" class="popup width315">
                <a class="close" href="#">Закрыть</a>
                <p class="font16">Для дальнейшей регистрации на нашем сайте просим выслать карточку основных сведений организации по адресу <a href="mailto:partner@enter.ru">partner@enter.ru</a></p>
                <p class="font16">Мы свяжемся с вами в течение 10 минут.</p>
            </div>
        </div>

        <div class="bCont__eRight">
            <ul class="bServicesList">
                <li class="bServicesList__eItem mCatItem"><a class="bServicesList__eLink" href="">Каталог товаров</a></li>
                <!--li class="bServicesList__eItem"><a class="bServicesList__eLink" href="">Специальные предложения</a></li>
                <li class="bServicesList__eItem"><a class="bServicesList__eLink" href="">Идеи для вашего бизнеса</a></li-->
            </ul>
        </div>
    </div>
</div>