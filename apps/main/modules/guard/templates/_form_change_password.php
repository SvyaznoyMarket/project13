<form action="<?php echo url_for('@user_changePassword') ?>" method="post">
    <div class="fl width430">
        <div class="pb20"><strong>Чтобы изменить пароль, укажите свой текущий пароль</strong></div>

        <?php echo $form ?>

        <div class="pb20">
            <input type="submit" value="Сохранить изменения" id="bigbutton" class="button yellowbutton">
        </div>        
        
        <div class="attention font11">Внимание! После смены пароля Вам придет письмо (SMS) с новым паролем</div>
    </div>
</form>