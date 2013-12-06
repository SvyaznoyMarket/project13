<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 */
?>

<div class="enterPrize">

	<h1 class="enterPrize__logo">Enter Prize</h1>

	<div class="bgPage mFailed"></div><!-- если пользователь уже получил купон то добавляем класс  mFailed-->

	<ul class="enterPrize__rules mFailed clearfix"><!-- если пользователь уже получил купон то добавляем класс  mFailed-->
		<li class="enterPrize__rules__item"><span class="sep">Вы уже получали фишку с этой страницы!</span> О новых фищках и предложениях ENTER PRIZE  мы обязательно<br/>сообщим Вам по  e-mail и мобильному телефону, который Вы указали </li>
	</ul>

	<ul class="enterPrize__rules clearfix" style="display: none;">
		<li class="enterPrize__rules__item"><span class="sep">Выбери</span> свою фишку со скидкой и жми получить!</li>
		<li class="enterPrize__rules__sep"></li>
		<li class="enterPrize__rules__item" style="width: 168px;"><span class="sep">Получи</span> номер фишки на E-mail и мобильный телефон, которые укажешь для участия в Enter Prize!</li>
		<li class="enterPrize__rules__sep"></li>
		<li class="enterPrize__rules__item"><span class="sep">Покупай</span> со скидкой, используя номер фишки при оплате!</li>
	</ul>

	<ul class="enterPrize__list clearfix">
		<li class="enterPrize__list__item mOrange">
			<a class="enterPrize__list__link" href="">
				<span class="cuponImg">
					<span class="cuponImg__inner">
						<span class="cuponIco"><img src="/styles/enterPrize/img/icoSec.png" /></span>

						<span class="cuponDesc">товары для дома</span>

						<span class="cuponPrice">101 <span class="rubl">p</span></span>
					</span>
				</span>

				<span class="cuponImgHover">
					<span class="cuponBtn">Получить</span>
				</span>
			</a>
		</li>

		<li class="enterPrize__list__item mBlue">
			<a class="enterPrize__list__link" href="">
				<span class="cuponImg">
					<span class="cuponImg__inner">
						<span class="cuponIco"><img src="/styles/enterPrize/img/icoSec.png" /></span>

						<span class="cuponDesc">товары для детей</span>

						<span class="cuponPrice">3%</span>
					</span>
				</span>

				<span class="cuponImgHover">
					<span class="cuponBtn">Получить</span>
				</span>
			</a>
		</li>

		<li class="enterPrize__list__item mPink mNoIco">
			<a class="enterPrize__list__link" href="">
				<span class="cuponImg">
					<span class="cuponImg__inner">
						<span class="cuponDesc">парфюмерия и косметика</span>

						<span class="cuponPrice">3%</span>
					</span>
				</span>

				<span class="cuponImgHover">
					<span class="cuponBtn">Получить</span>
				</span>
			</a>
		</li>

		<li class="enterPrize__list__item mGreen mNoIco mLast">
			<a class="enterPrize__list__link" href="">
				<span class="cuponImg">
					<span class="cuponImg__inner">
						<span class="cuponDesc">парфюмерия и косметика и много-много другого текста</span>

						<span class="cuponPrice">3%</span>
					</span>
				</span>

				<span class="cuponImgHover">
					<span class="cuponBtn">Получить</span>
				</span>
			</a>
		</li>
	</ul>
</div>