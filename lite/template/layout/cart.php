<?
/**
 * @var $page \View\Cart\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<?= $page->blockHeader() ?>

<div class="wrapper">
    <div class="middle">
    	<div class="container">
            <main class="content">
                <div class="content__inner">
                    <!-- категории товаров -->
                    <div class="section">
                        <div class="section__title section__title_h1">Корзина</div>

                        <div class="cart table">
                        	<div class="cart__row table-row">
                        		<div class="cart__image cart__cell table-cell"><img src="http://8.imgenter.ru/uploads/media/42/12/58/thumb_0120_product_160.jpeg" alt="" class="image"></div>

                        		<div class="cart__desc cart__cell table-cell">
                        			<div class="cart__product-name">Планшетный компьютер Apple iPad mini 2 with Retina display Wi-Fi 16 ГБ серебристый</div>
									<div class="cart__product-price">1222 <span class="rubl-css">P</span></div>
                        		</div>

                        		<div class="cart__counter cart__cell table-cell">
                        			<div class="counter">
                        				<button class="counter__btn counter__btn_minus disabled"></button>
                        				<input type="text" class="counter__it" value="10">
                        				<button class="counter__btn counter__btn_plus"></button>
                        				<span class="counter__num">шт.</span>
                        			</div>

                        			<a class="cart__delete" href=""><span class="dotted">Удалить</span></a>
                        		</div>

                        		<div class="cart__summ cart__cell table-cell">1222 <span class="rubl-css">P</span></div>
                        	</div>

                        	<div class="cart__row table-row">
                        		<div class="cart__image cart__cell table-cell"><img src="http://f.imgenter.ru/uploads/media/71/ca/a9/thumb_a04f_product_160.jpeg" alt="" class="image"></div>

                        		<div class="cart__desc cart__cell table-cell">
                        			<div class="cart__product-name">Горшок</div>
									<div class="cart__product-price">99 <span class="rubl-css">P</span></div>
                        		</div>

                        		<div class="cart__counter cart__cell table-cell">
                        			<div class="counter">
                        				<button class="counter__btn counter__btn_minus disabled"></button>
                        				<input type="text" class="counter__it" value="10">
                        				<button class="counter__btn counter__btn_plus"></button>
                        				<span class="counter__num">шт.</span>
                        			</div>

                        			<a class="cart__delete" href=""><span class="dotted">Удалить</span></a>
                        		</div>

                        		<div class="cart__summ cart__cell table-cell">1222 <span class="rubl-css">P</span></div>
                        	</div>

                        	<div class="cart__row table-row">
                        		<div class="cart__image cart__cell table-cell"><img src="http://8.imgenter.ru/uploads/media/42/12/58/thumb_0120_product_160.jpeg" alt="" class="image"></div>

                        		<div class="cart__desc cart__cell table-cell">
                        			<div class="cart__product-name">Планшетный компьютер Apple iPad mini 2 with Retina display Wi-Fi 16 ГБ серебристый</div>
									<div class="cart__product-price">1222 <span class="rubl-css">P</span></div>
                        		</div>

                        		<div class="cart__counter cart__cell table-cell">
                        			<div class="counter">
                        				<button class="counter__btn counter__btn_minus disabled"></button>
                        				<input type="text" class="counter__it" value="10">
                        				<button class="counter__btn counter__btn_plus"></button>
                        				<span class="counter__num">шт.</span>
                        			</div>

                        			<a class="cart__delete" href=""><span class="dotted">Удалить</span></a>
                        		</div>

                        		<div class="cart__summ cart__cell table-cell">1222 <span class="rubl-css">P</span></div>
                        	</div>
                        </div>

                        <div class="cart-summ">
	                    	<div class="cart-summ__title">Сумма заказа:</div>
	                    	<div class="cart-summ__price">103 990 <span class="rubl-css">P</span></div>
	                    </div>

	                    <div class="cart-order">
	                    	<div class="cart-order__back"><a class="cart-order__back-link" href="" title=""><span class="underline">Вернуться к покупкам</span></a></div>
	                    	<div class="cart-order__btn"><a href="" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a></div>
	                    </div>
                    </div>

                    <?= $page->blockViewed() ?>
                </div>
			</main>
		</div>

        <aside class="left-bar">
            <?= $page->blockNavigation() ?>
        </aside>
    </div>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
