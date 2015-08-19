<div class="popup-feedback popup popup_440 js-feedback-popup" style="display: none;">
    <div class="popup__close js-popup-close">×</div>
        <div class="popup__title">Обратная связь</div>

    <form action="<?= $page->url('feedback.send') ?>" enctype="multipart/form-data" class="feedback-form form js-feedback-form" method="post">
        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-email" data-required="true" name="email" value="">
            <label for="" class="form__placeholder placeholder placeholder_str">Ваш email</label>
        </div>

        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-topic" data-required="true" name="subject" value="">
            <label for="" class="form__placeholder placeholder placeholder_str">Тема письма</label>
        </div>

        <div class="form__field">
            <textarea name="message" id="" data-required="true" class="textarea js-feedback-text"></textarea>
            <label for="" class="form__placeholder placeholder placeholder_str">Текст письма</label>
        </div>

        <div class="form__field">
            <input type="file">
        </div>

        <div class="btn-container">
            <button class="btn-primary btn-primary_bigger js-feedback-submit">Отправить</button>
        </div>
    </form>

    <div class="feedback-complete">
        <div class="feedback-complete__text"><span class="mark">Спасибо!</span></br>Мы обязательно рассмотрим ваше обращение.</div>

        <div class="btn-container"><a href="" class="btn-normal btn-normal_middle js-popup-close">Продолжить</a></div>
    </div>
</div>
