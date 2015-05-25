<div class="suppliers__head">
    <div class="suppliers__head-inn">
        <div class="suppliers__tl"><i class="suppliers-icon"></i>Регистрация поставщика</div>
        <div class="suppliers-login"><span class="suppliers-login__tl">Регистрировались?</span>
            <a href="#" class="suppliers-login__lk supply-btn">Войти</a>
        </div>
    </div>
</div>
<div class="suppliers__cnt">
    <div class="suppliers__slogan">Мы умеем продавать!</div>
    <img class="suppliers__sl-icon" src="/styles/suppliers/img/sup-icons.png">
    <ul class="suppliers-our-nums-list clearfix">
        <li class="suppliers-our-nums-list__i">
            <span class="suppliers-our-nums-list__num week-orders">115&thinsp;000</span>
            <span class="suppliers-our-nums-list__txt">заказов в неделю</span>
        </li>
        <li class="suppliers-our-nums-list__i">
            <span class="suppliers-our-nums-list__num day-watchers">500&thinsp;000</span>
            <span class="suppliers-our-nums-list__txt">уникальных посетителей в день</span>
        </li>
        <li class="suppliers-our-nums-list__i">
            <span class="suppliers-our-nums-list__num month-volume">1&thinsp;800&thinsp;000&thinsp;000</span>
            <span class="suppliers-our-nums-list__txt">оборот в месяц, руб.</span>
        </li>
    </ul>
    <h4 class="suppliers-collab__head">Cотрудничество с нами это:</h4>
    <ul class="suppliers-collab-list">
        <li class="suppliers-collab-list__i">новые возможности для вашего бизнеса;</li>
        <li class="suppliers-collab-list__i">доступ к крупным каналам продаж;</li>
        <li class="suppliers-collab-list__i">доставка по всей России;</li>
        <li class="suppliers-collab-list__i">более 1000 точек самовывоза.</li>
    </ul>
    <div class="suppliers__short-form">
        <div class="supply-btn__wrap">
            <a href="#" class="supply-btn supply-btn-big">Стать партнером</a>
        </div>
    <div class="suppliers-collab__info">По всем вопросам звоните +7 (495) 775-00-06</div>

        <form   action="<?= \App::helper()->url('supplier.new') ?>"
                method="post">
            
            <div class="suppliers__slogan">Мы умеем продавать!</div>

            <div class="control-group">
                <label class="control-group__lbl">Наименование организации</label>
                <input name="detail[name]" class="control-group__input error" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Форма собственности</label>
                <div class="custom-select custom-select--suppliers">
                    <select name="detail[legal_type]" class="custom-select__inn">
                        <option class="custom-select__i">Индивидуальный предприниматель</option>
                        <option class="custom-select__i">Общество с ограниченной ответственностью</option>
                        <option class="custom-select__i">Акционерное общество</option>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Контактное лицо</label>
                <input name="first_name" class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">E-mail</label>
                <input name="email" class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Мобильный телефон</label>
                <input name="mobile" class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <input type="checkbox" class="customInput customInput-checkbox js-customInput" id="accept" name="" value="">

                <label class="customLabel customLabel-checkbox" for="accept">
                    Принимаю условия <a class="suppliers-offer" href="#">договора оферты</a>
                </label>
          
            </div>
            <div class="supply-btn__wrap">
                <input type="submit" class="supply-btn supply-btn-big" value="Стать партнером" />
            </div>
        </form>
    </div>
</div>

<div class="suppliers-popup" style="display: none">
    <div class="popupFl_clsr"></div>
    <div class="suppliers-popup__tl">Вход в Enter B2B</div>
    <form>
        <div class="control-group">
                <label class="control-group__lbl">Логин</label>
                <input class="control-group__input" placeholder="">
            </div>
            <div class="control-group">
                <label class="control-group__lbl">Пароль</label>
                <input class="control-group__input forgot-password__block" placeholder="">
                <a href="#" class="forgot-password">забыли?</a>
            </div>
            <div class="control-group supply-btn__lo">
                <a href="#" class="suppliers-login__lk supply-btn">Войти</a>
            </div>
    </form>
</div>
