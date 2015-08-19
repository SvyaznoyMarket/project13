<div class="popup-feedback popup popup_440 js-feedback-popup" style="display: none;">
    <div class="popup__close js-popup-close">×</div>
    <div class="popup__title">Обратная связь</div>

    <form action="<?= $page->url('feedback.send') ?>" class="form" method="post">
        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-email" name="email" value="">
            <label for="" class="form__placeholder placeholder">Ваш email</label>
        </div>

        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-topic" name="subject" value="">
            <label for="" class="form__placeholder placeholder">Тема письма</label>
        </div>

        <div class="form__field">
            <textarea name="message" id="" class="textarea js-feedback-text"></textarea>
            <label for="" class="form__placeholder placeholder">Текст письма</label>
        </div>

        <div class="form__field">
            <input type="file">
        </div>

        <div class="btn-container">
            <button class="btn-primary btn-primary_bigger js-feedback-submit">Отправить</button>
        </div>
    </form>
</div>