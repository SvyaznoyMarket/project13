<?php
/**
 * @var $page               \View\User\OrdersPage
 * @var $helper             \Helper\TemplateHelper
 * @var $user               \Session\User
 */
?>

<div class="personalPage">
    <?= $page->render('user/_menu', ['page' => $page]) ?>
	<div class="personalTitle">Избранное</div>

	<div class="table-favorites table table--border-cell-hor">
		<div class="table-row">
			<div class="table-favorites__cell-left table-cell"><img src="http://fs08.enter.ru/1/1/120/43/331370.jpg" alt="" class="table-favorites__img"></div>

			<div class="table-favorites__cell-center table-cell">
				<div class="table-favorites__name">Назввание товара</div>
				<div class="table-favorites__price">500 <span class="rubl">p</span></div>
			</div>

			<div class="table-favorites__cell-right table-cell">
				<div class="btnBuy"><a href="" class="btn-type btn-type--buy js-orderButton jsBuyButton">В корзину</a></div>
				<div class="table-favorites__delete"><a href="" class="undrl">Удалить</a></div>
			</div>
		</div>

		<div class="table-row">
			<div class="table-favorites__cell-left table-cell"><img src="http://fs08.enter.ru/1/1/120/43/331370.jpg" alt="" class="table-favorites__img"></div>

			<div class="table-favorites__cell-center table-cell">
				<div class="table-favorites__name">Назввание товара</div>
				<div class="table-favorites__price">500 <span class="rubl">p</span></div>
			</div>

			<div class="table-favorites__cell-right table-cell">
				<div class="btnBuy"><a href="" class="btn-type btn-type--buy js-orderButton jsBuyButton">В корзину</a></div>
				<div class="table-favorites__delete"><a href="" class="undrl">Удалить</a></div>
			</div>
		</div>
	</div>
</div>
