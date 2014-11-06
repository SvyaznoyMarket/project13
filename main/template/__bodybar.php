<?php
return function($showBodybar) { ?>

    <div class="bodybar <?php if (!$showBodybar): ?>bodybar-hide<?php endif ?> js-bodybar">
        <form action="" class="sbscrBar js-subscribebar">
            <label for="" class="sbscrBar_lbl">Сообщайте мне об акциях и специальных ценах</label>
            <div class="sbscrBar_itw">
                <input type="text" name="" id="" class="sbscrBar_it js-subscribebar-email" placeholder="Ваш e-mail">
                <div class="sbscrBar_errtx">Неверно введен email</div>
            </div>
            <input type="submit" value="Подписаться" class="sbscrBar_is btn3 js-subscribebar-subscribeButton">
            <div class="sbscrBar_tx">и получить купон на 300 руб.</div>
        </form>

        <div class="bodybar_clsr js-bodybar-hideButton">&#215;</div>
    </div>

<? };
