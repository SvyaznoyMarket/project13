<div class="call-back-btn js-callback-button">
    <? if(true): ?><div class="call-back-btn__alert">!</div><? endif; ?>

    <? if(true): ?>
        <img src="/styles/callback/img/phone.png" alt="#">
        <? else: ?>
        <img src="/styles/callback/img/bell.png" alt="#" style="top: 56%;">
    <? endif; ?>
    <div class="call-back-btn__inner-txt" style="display:none;">Нужна помощь</div>
    <div class="call-back-btn__txt js-callback-button-txt">Помогу найти</div>
</div>



<div class="js-callback-popup callback-popup">
    <div class="callback-popup__content js-callback-popup-content">
        <a class="js-callback-popup-close callback-popup__close" href="#">&times;</a>
        <div class="callback-popup__cell">
            <? if(true): ?>
                <img src="/styles/callback/img/phone-b.png" alt="#">
            <? else: ?>
                <img src="/styles/callback/img/bell-b.png" alt="#">
            <? endif; ?>
        </div>

        <div class="callback-popup__cell callback-popup__cell_right">
            <div class="callback-popup__title">
            <span>Оставьте номер телефона<br>
            и мы вам перезвоним
            </span>
            </div>
            <form action="#">
                <label class="callback-popup__phone">
                    <input type="text" >
                </label>

                <fieldset class="callback-popup__btn-block">
                    <input class="callback-popup__btn" type="submit" value="Перезвонить мне">
                </fieldset>
            </form>
        </div>
    </div>
</div>