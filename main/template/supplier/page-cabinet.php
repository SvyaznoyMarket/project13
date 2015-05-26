<?
/**
 * @var $userEntity \Model\User\Entity
 * @var $userPrices \Model\Supplier\File[]
 */
?>

<div class="suppliers__head clearfix">
    <div class="suppliers__head-inn clearfix">
        <div class="suppliers__tl"><i class="suppliers-icon"></i>
            <div class="user">
                <div class="user__name"><?= $userEntity->legalDetails['name'] ?></div>
                <ul class="user-info-list">
                    <li class="user-info-list__i"><?= $userEntity->getName() ?></li>
                    <li class="user-info-list__i"><?= $userEntity->getMobilePhone() ?></li>
                    <li class="user-info-list__i"><?= $userEntity->getEmail() ?></li>
                </ul>
            </div>
        </div>
        <div class="suppliers__right-top">
            <span class="suppliers__support">По всем вопросам звоните<br>+7 (495) 775-00-06</span>
            <div class="btn-wrap"><a class="load-btn jsEditDetails" href="#">Изменить</a></div>
        </div>
        <div class="user-info-text">
            <? if ($userEntity->isLegalDetailsFull()) : ?>
                <?= $userEntity->legalDetails['legal_address'] ?><br>
                ИНН <?= $userEntity->legalDetails['inn'] ?><br>
                КПП <?= $userEntity->legalDetails['kpp'] ?><br>
                Р/С <?= $userEntity->legalDetails['account'] ?><br>
                К/С <?= $userEntity->legalDetails['korr_account'] ?>
            <? endif ?>
        </div>
    </div>
</div>
<div class="suppliers__cnt suppliers__sect-cnt">
    <div class="suppliers__sect suppliers__files">
        <div class="suppliers__sect-tl">Загрузить файл</div>
        <ul class="suppliers-load-list">
            <ol class="suppliers-load-list__i">Скачайте <a class="suppliers-load" href="/Enter-price-template.xlsx"><i class="suppliers-load__icon"></i><span class="suppliers-load__inn">Шаблон прайс-листа</span></a></ol>
            <ol class="suppliers-load-list__i">Заполните данные</ol>
            <ol class="suppliers-load-list__i">Загрузите прайс-лист на сайт: <div class="btn-wrap">
                    <form id="priceForm"
                          action="<?= \App::helper()->url('supplier.load') ?>"
                          method="post"
                          enctype="multipart/form-data"
                          style="height: 0">
                        <input type="file"
                               id="priceInput"
                               name="priceFiles"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                               style="visibility: hidden" />
                    </form>
                    <button id="priceButton" class="load-btn">Выбрать файл</button>
                </div></ol>
        </ul>
    </div>

    <div class="suppliers__form suppliers__sect">
        <? if ($userPrices) : ?>
            <div class="prices">
                <div class="suppliers__sect-tl">Ваши прайс-листы</div>
                <ul class="prices-list jsPricesList">
                    <? foreach ($userPrices as $key => $price) : ?>
                        <?= $page->render('supplier/_file', ['file' => $price]) ?>
                    <? endforeach ?>
                </ul>
            </div>
        <? endif ?>

            <form action="<?= \App::helper()->url('supplier.update') ?>"
                  id="detailsForm"
                  method="post"
                  style="display: <?= $userEntity->isLegalDetailsFull() ? 'none':'block' ?>">
                <div class="suppliers__sect-tl">Зарегистрировать поставщика</div>
                <div class="control-group">
                    <label class="control-group__lbl">Наименование юридического лица</label>
                    <input name="detail[name]" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['name'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Полное наименование юридического лица</label>
                    <input name="detail[name_full]" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['name_full'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Форма собственности</label>
                    <div class="custom-select custom-select--suppliers">
                        <select name="detail[legal_type]" class="custom-select__inn">
                            <option value="ИП" <?= $userEntity->legalDetails['legal_type'] == 'ИП' ? 'selected' : '' ?>>Индивидуальный предприниматель (ИП)</option>
                            <option value="ООО" <?= $userEntity->legalDetails['legal_type'] == 'ООО' ? 'selected' : '' ?>>Общество с ограниченной ответственностью (ООО)</option>
                            <option value="ОАО" <?= $userEntity->legalDetails['legal_type'] == 'ОАО' ? 'selected' : '' ?>>Открытое Акционерное общество (ОАО)</option>
                            <option value="ЗАО" <?= $userEntity->legalDetails['legal_type'] == 'ЗАО' ? 'selected' : '' ?>>Закрытое Акционерное общество (ЗАО)</option>
                            <option value="Другая форма" <?= $userEntity->legalDetails['legal_type'] == 'Другая форма' ? 'selected' : '' ?>>Другая форма</option>
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Юридический адрес</label>
                    <input name="detail[legal_address]" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['legal_address'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Фактический адрес</label>
                    <input name="detail[real_address]" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['real_address'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">ИНН</label>
                    <input name="detail[inn]" data-mask="9999999999?99" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['inn'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">КПП</label>
                    <input name="detail[kpp]" data-mask="999999999" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['kpp'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Расчетный счет</label>
                    <input name="detail[account]" data-mask="9999999999999?9999999999" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['account'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Корреспондентский счет</label>
                    <input name="detail[korr_account]" data-mask="99999999999999999999" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['korr_account'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">БИК</label>
                    <input name="detail[bik]" data-mask="999999999" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['bik'] ?>">
                </div>
                <div class="control-group">
                    <label class="control-group__lbl">Код ОКПО</label>
                    <input name="detail[okpo]" data-mask="99999999" class="control-group__input" placeholder="" value="<?= $userEntity->legalDetails['okpo'] ?>">
                </div>
                <div class="control-group">
                    <input class="supply-btn" value="Сохранить" type="submit">
                    <div class="details-info"><span class="jsAddressSaved" style="display: none; color: #3cb371">Реквизиты сохранены.</span> <span class="jsFillFormSpan" style="display: <?= $userEntity->isLegalDetailsFull() ? 'none' : 'inline' ?>">Заполните все поля.</span></div>
                </div>
            </form>
    </div>

</div>