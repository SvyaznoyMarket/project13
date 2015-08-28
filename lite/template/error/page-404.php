<!doctype html>
<html class="no-js" lang="">

	<?= $page->blockHead() ?>

	<body>
		<?= $page->blockHeader() ?>

		<div class="wrapper wrapper-content">
            <main class="content">
				<div class="section">
				    <div class="erro-page" style="background-image: url('/public/images/404/bg/<?= mt_rand(1, 4) ?>.png');">
				        <div class="erro-page__code">404</div>
				        <div class="erro-page__text">Страница не найдена</div>

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
