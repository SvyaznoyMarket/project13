<div id="enterprize-identify" class="popup">
    <i class="close" title="Закрыть">Закрыть</i>

    <div class="enterprizeAuthBox__title"></div>

    <div class="bFormLogin jsBodyWrapper">
        <?=$page->render('form-login',[
            'form' => new \View\User\LoginForm()
        ])?>

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
    // @todo положить экземпляр в какую-нибудь глобальную переменную
    window.registerAuth = $('#enterprize-identify').registerAuth({
        state: 'confirm',
        beforeInit: function (self, callback) {
            $(self.element).lightbox_me({
                centered: true,
                autofocus: true,
                onLoad: function () {
                    callback();
                }
            });
        },
        afterComplete: function(self, callback) {
            $(self.element).trigger('close.lme');
        }
    }).data('ui-registerAuth');
    window.registerAuth.init();
});
</script>