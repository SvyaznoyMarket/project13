<!doctype html>
<html class="no-js" lang="">

    <?= $page->blockHead() ?>

    <body>
        <?= $page->blockHeader() ?>

        <div class="wrapper wrapper-content">
            <main class="content">
                <div class="section">
                    <div class="erro-page erro-page_500">
                        <div class="erro-page__code">500</div>
                        <div class="erro-page__text">На данный момент ресурс недоступен.<br/>Ведутся профилактические работы.<br/>Приносим свои извинения за предоставленные неудобства.</div>

                        <a href="/" class="erro-page__continue btn-primary btn-primary_bigger">Вернуться на главную</a>
                    </div>
                </div>
           </main>
        </div>

        <hr class="hr-orange">

        <?= $page->blockFooter() ?>

        <?= $page->slotBodyJavascript() ?>

        <?= $page->blockUserConfig() ?>

        <?= $page->blockPopupTemplates() ?>
    </body>
</html>
