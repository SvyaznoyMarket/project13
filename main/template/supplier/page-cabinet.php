<div class="suppliers__head">
    <div class="suppliers__head-inn clearfix">
        <div class="suppliers__tl"><i class="suppliers-icon"></i>
            <div class="user">
                <div class="user__name">ООО Пупкин и партнеры 42</div>
                <ul class="user-info-list">
                    <li class="user-info-list__i">Феофан Фролов</li>
                    <li class="user-info-list__i">+7 (495) 775-00-06</li>
                    <li class="user-info-list__i">feofan@pupkin42.ru</li>
                </ul>
            </div>
        </div>
        <div class="suppliers__right-top">
            <span class="suppliers__support">По всем вопросам звоните<br>+7 (495) 775-00-06</span>
            <div class="btn-wrap"><a class="load-btn" href="#">Изменить</a></div>
        </div>
        <div class="user-info-text">
            107045, г . Москва, Луков Переулок, д. 7<br>
            ИНН 7714259876<br>
            КПП 770801001<br>
            Р/С 40702810480900017838<br>
            К/С 30101810900000000767
        </div></div>
</div>
<div class="suppliers__cnt suppliers__sect-cnt">
    <div class="suppliers__sect suppliers__files">
        <div class="suppliers__sect-tl">Загрузить файл</div>
        <ul class="suppliers-load-list">
            <ol class="suppliers-load-list__i">Скачайте <a class="suppliers-load" href="#"><i class="suppliers-load__icon"></i><span class="suppliers-load__inn">Шаблон прайс-листа</span></a></ol>
            <ol class="suppliers-load-list__i">Заполните данные</ol>
            <ol class="suppliers-load-list__i">Загрузите прайс-лист на сайт: <div class="btn-wrap">
                    <form action="<?= \App::helper()->url('supplier.load') ?>" style="height: 0">
                        <input type="file"
                               id="priceInput"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                               style="visibility: hidden" />
                    </form>
                    <button id="priceButton" class="load-btn">Выбрать файл</button>
                </div></ol>
        </ul>
    </div>

    <div class="suppliers__form suppliers__sect">
        <div class="prices">
            <div class="suppliers__sect-tl">Ваши прайс-листы</div>
            <ul class="prices-list">
                <li class="prices-list__i">
                    <i class="suppliers-load__icon"></i><span class="prices-list__file-name">Книга-1.xlsx</span>
                    <span class="prices-list__date">22.05.2015</span>
                </li>
                <li class="prices-list__i">
                    <i class="suppliers-load__icon"></i><span class="prices-list__file-name">Книга-1.xlsx</span>
                    <span class="prices-list__date">22.05.2015</span>
                </li>
                <li class="prices-list__i">
                    <i class="suppliers-load__icon"></i><span class="prices-list__file-name">Книга-1.xlsx</span>
                    <span class="prices-list__date">22.05.2015</span>
                </li>
                <li class="prices-list__i">
                    <i class="suppliers-load__icon"></i><span class="prices-list__file-name">Книга-1.xlsx</span>
                    <span class="prices-list__date">22.05.2015</span>
                </li>
            </ul>
        </div>
        <form>
            <div class="suppliers__sect-tl">Зарегистрировать поставщика</div>
            <div class="control-group">
                <label class="control-group__lbl">Форма собственности</label>
                <div class="custom-select custom-select--suppliers">
                    <select class="custom-select__inn">
                        <option class="custom-select__i">Индивидуальный предприниматель</option>
                        <option class="custom-select__i">Общество с ограниченной ответственностью</option>
                        <option class="custom-select__i">Акционерное общество</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Юридический адрес</label>
                <input class="control-group__input error" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Фактический адрес</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">ИНН</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">КПП</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Расчетный счет</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Корреспондентский счет</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">БИК</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Код ОКПО</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <input class="supply-btn" value="Сохранить" type="submit">
                <div class="details-info">Реквизиты сохранены. Заполните все поля.</div>
            </div>
        </form>
    </div>

</div>