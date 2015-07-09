<?php
/**
 * Created by PhpStorm.
 * User: vadimkovalenko
 * Date: 22.07.14
 * Time: 15:15
 * $isEmailConfirmed bool
 * $isPhoneConfirmed bool
 */
?>
<div class="enterprizeConfirm">
    <?php if (!$isEmailConfirmed && 0===1): // отключили?>
        <div class="jsEmailConfirm">
            <h3>Подтвердите пожалуйста email</h3>
            <form class="confirmForm jsConfirmEmail" action="<?=\App::router()->generate('enterprize.confirmAll.email') ?>" method="post">
                <label class="labelCode">Код</label>
                <input type="text" class="text" name="code" />
                <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
            </form>

            <form class="confirmForm jsConfirmEmailRepeatCode" action="<?= \App::router()->generate('enterprize.confirmAll.createEmail') ?>" method="post">
                <input type="hidden" name="isRepeatRending" value="true" />
                <input style="margin-left: 0;" class="confirmCode bigbutton" type="submit" value="Отправить повторно" />
            </form>

            <p style="margin: 30px 0 0 0; font-size: 12px;" class="textConfirm">
                Если письмо затерялось или обнаружили ошибку, пожалуйста, напишите нам на <a style="text-decoration: underline;" href="http://my.enter.ru/feedback/">my.enter.ru/feedback/</a>.
            </p>
        </div>
    <?php endif; ?>

    <?php if(!$isPhoneConfirmed): ?>
        <div class="jsPhoneConfirm">
            <h3>Подтвердите пожалуйста телефон</h3>
            <form class="confirmForm jsConfirmPhone" action="<?= \App::router()->generate('enterprize.confirmAll.phone') ?>" method="post">
                <label class="labelCode">Код</label>
                <input type="text" class="text" name="code" />
                <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
            </form>

            <form class="confirmForm jsConfirmPhoneRepeatCode" action="<?= \App::router()->generate('enterprize.confirmAll.createPhone') ?>" method="post">
                <input type="hidden" name="isRepeatRending" value="true" />
                <input type="submit" class="confirmCode bigbutton" value="Отправить код подтверждения" />
            </form>
        </div>
    <?php endif; ?>

    <?php if (!$isEmailConfirmed): ?>
        <div class="jsEmailConfirm">
            <br/><h3>Подтердите пожалуйста email</h3>
            <form class="confirmForm jsCheckConfirmForm" action="<?=\App::router()->generate('enterprize.confirmAll.state') ?>" method="post">
<!--                <label class="labelCode">На Ваш почтовый ящик был выслан email со ссылкой активации</label><br/>-->
                <input type="submit" class="confirmCode bigbutton" value="Я подтердил email" />
            </form>

            <form class="confirmForm jsConfirmEmailRepeatCode" action="<?= \App::router()->generate('enterprize.confirmAll.createEmail') ?>" method="post">
                <input type="hidden" name="isRepeatRending" value="true" />
                <input style="margin-left: 0;" class="confirmCode bigbutton" type="submit" value="Отправить письмо" />
            </form>
            <p style="margin: 30px 0 0 0; font-size: 12px;" class="textConfirm">
                Если письмо затерялось или обнаружили ошибку, пожалуйста, напишите нам на <a style="text-decoration: underline;" href="http://my.enter.ru/feedback/">my.enter.ru/feedback/</a>.
            </p>
        </div>
    <?php endif; ?>
</div>

