<!doctype html>
<html class="no-js" lang="">

	<?= $page->blockHead() ?>

	<body>
		<?= $page->blockHeader() ?>

		<div class="wrapper wrapper-content">
            <main class="content">
				<div class="section">
				    <div class="error-page">
				    	<div class="error-page__title">
					        <div class="error-page__code">404</div>
					        <div class="error-page__text">Страница не найдена</div>
				        </div>

				        <a href="/" class="error-page__continue btn-primary btn-primary_bigger">Вернуться на главную</a>
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
