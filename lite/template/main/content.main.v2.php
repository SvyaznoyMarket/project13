<main class="content">
    <div class="main-banner">

    </div>

    <div class="section">
        <div class="main-categories grid-transform-4col">
            <div class="grid-transform-4col__row">
                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item magenta">
                        <a href="/catalog/household" class="main-categories__link">
                            <span class="main-categories__name">Всё для дома</span>
                            <span class="main-categories__sub-name"></span>
                            <img src="/public/images/main-page/categories/cat1.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item violet">
                        <a href="/catalog/electronics/telefoni-smartfoni-2348" class="main-categories__link">
                            <span class="main-categories__name">Будь на связи</span>
                            <span class="main-categories__sub-name">Смартфоны</span>
                            <img src="/public/images/main-page/categories/cat2.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col2">
                    <div class="main-categories__item blue">
                        <a href="/catalog/furniture/myagkaya-mebel-divani-147" class="main-categories__link">
                            <span class="main-categories__name">Расслабиться после рабочего дня</span>
                            <span class="main-categories__sub-name">Диваны</span>
                            <img src="/public/images/main-page/categories/cat3.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid-transform-4col__row">
                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item">
                        <a href="/catalog/children/gigiena-i-uhod-za-malishom-podguzniki-i-nakopiteli-dlya-podguznikov-1221" class="main-categories__link">
                            <span class="main-categories__name">Чистые штанишки</span>
                            <span class="main-categories__sub-name">Подгузники</span>
                            <img src="/public/images/main-page/categories/cat4.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col2">
                    <div class="main-categories__item orange">
                        <a href="/catalog/children/igrushki-i-igri-myagkie-igrushki-354" class="main-categories__link">
                            <span class="main-categories__name">Мягкие игрушки</span>
                            <span class="main-categories__sub-name"></span>
                            <img src="/public/images/main-page/categories/cat5.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item green">
                        <a href="/catalog/tovari-dlya-givotnih" class="main-categories__link">
                            <span class="main-categories__name">Любимым питомцам</span>
                            <span class="main-categories__sub-name"></span>
                            <img src="/public/images/main-page/categories/cat6.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid-transform-4col__row">
                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item olive">
                        <a href="/catalog/parfyumeriya-i-kosmetika" class="main-categories__link">
                            <span class="main-categories__name">Быть еще лучше</span>
                            <span class="main-categories__sub-name">Парфюмерия и косметика</span>
                            <img src="/public/images/main-page/categories/cat10.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item blue">
                        <a href="/catalog/jewel/koltsa-966" class="main-categories__link">
                            <span class="main-categories__name">Кольца</span>
                            <span class="main-categories__sub-name"></span>
                            <img src="/public/images/main-page/categories/cat9.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item grey">
                        <a href="/catalog/do_it_yourself/aksessuari-dlya-avtomobiley-225" class="main-categories__link">
                            <span class="main-categories__name">Для авто</span>
                            <span class="main-categories__sub-name"></span>
                            <img src="/public/images/main-page/categories/cat8.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>

                <div class="grid-transform-4col__col col1">
                    <div class="main-categories__item magenta">
                        <a href="/catalog/furniture/matrasi-1193" class="main-categories__link">
                            <span class="main-categories__name">Крепкий сон</span>
                            <span class="main-categories__sub-name">Матрасы</span>
                            <img src="/public/images/main-page/categories/cat7.png" alt="" class="main-categories__img">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $page->render('main/_brands.popular') ?>

    <?= $page->blockViewed() ?>
</main>

<div class="popup-feedback popup popup_440 js-feedback-popup" style="display: none;">
    <div class="popup__close js-popup-close">×</div>
    <div class="popup__title">Обратная связь</div>

    <form action="" class="form">
        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-email" name="" value="">
            <label for="" class="form__placeholder placeholder">Ваш email</label>
        </div>

        <div class="form__field">
            <input id="" type="text" class="form__it it js-feedback-topic" name="" value="">
            <label for="" class="form__placeholder placeholder">Тема письма</label>
        </div>

        <div class="form__field">
            <textarea name="" id="" class="textarea js-feedback-text"></textarea>
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