<div id="enterprize-identify" class="popup" style="width: 724px; display: none;">
    <i class="close" title="Закрыть">Закрыть</i>

    <div class="enterprizeAuthBox__title"></div>

    <div class="bFormLogin jsBodyWrapper">
        <?php $form = new \View\User\LoginForm()?>
        <form action="<?= $page->url($form->getRoute(),['redirect_to'=>(isset($redirect_to)?$redirect_to:null)]) ?>" class="form bFormLogin__ePlace jsLoginForm" method="post">
            <fieldset class="bFormLogin__ePlace">
                <legend class="bFormLogin__ePlaceTitle">Мои логин и пароль</legend>
                <label class="bFormLogin__eLabel">E-mail или мобильный телефон:</label>
                <div><input class="text bFormLogin__eInput jsSigninUsername" type="text" value="<?= $form->getUsername() ?>" name="signin[username]" /></div>
                <label class="bFormLogin__eLabel">Пароль:</label>
                <div><input class="text bFormLogin__eInput jsSigninPassword" type="password" name="signin[password]" /></div>
                <input type="submit" class="bigbutton bFormLogin__eBtnSubmit jsSubmit" data-loading-value="Вхожу..." value="<?=$form->getSubmit()?>" />
            </fieldset>
        </form>

        <div class="bFormLogin__ePlace">
            <?=$page->render('enterprize/form-registration',[
                'form' => (new \View\Enterprize\Form())
                        ->setRoute('user.registrationExtended')  // Кидаем на регистрацию с расширенным набором данных
                        ->setSubmit('Регистрация'),
            ])?>
        </div>
    </div>
</div>

<script type="text/javascript">
$(window).load(function () {
    window.registerAuth = $('#enterprize-identify').registerAuth({
        beforeInit: function (self, callback) {
            $(self.element).lightbox_me({
                centered: true,
                autofocus: true,
                onLoad: function () {
                    callback();
                }
            });
            return self;
        },
        afterComplete: function(self) {
            $(self.element).trigger('close.lme');
            return self;
        }
    }).data('ui-registerAuth');
});
</script>