<?php
return function(\Helper\TemplateHelper $helper, $class = null) { ?>
    
    <form action="" class="sbscrBar clearfix <?= $helper->escape($class) ?> js-subscribebar">
        <label for="" class="sbscrBar_lbl">Сообщайте мне об акциях и специальных ценах</label>
        <div class="sbscrBar_itw">
            <input type="text" name="" id="" class="sbscrBar_it js-subscribebar-email" placeholder="Ваш e-mail">
        </div>
        <input type="submit" value="Подписаться" class="sbscrBar_is js-subscribebar-subscribeButton">
        <div class="sbscrBar_tx">и получить купон на 300 руб.</div>
    </form>
    
<? };