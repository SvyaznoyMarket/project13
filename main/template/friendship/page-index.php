<?php
/**
 * @var $page           \View\DefaultLayout
 * @var $user           \Session\User
 */
?>

<div class="content-section clearfix">
	<div class="content-section__text">
		Почему? 
		<div class="title title_blue">Рассказываем!</div>
		Вы первые узнаете обо всех наших распродажах и суперакциях!
		<div class="title title_green">Показываем! </div>
		Все самые интересные новинки рынка!
		<div class="title title_orange">Слушаем! </div>
		Есть вопросы по товарам или пожелания по улучшению работы?
		<a href="mailto:feedback@enter.ru">Напишите нам</a> и мы вам ответим!
	</div>

	<div class="content-section__form">
		<div class="subscribe-form clearfix">
			<div class="subscribe-form__title">Будем друзьями?</div>
		    <input class="subscribe-form__email flocktory_email" placeholder="Введите ваш email" />
		    <button class="subscribe-form__btn run_flocktory_popup" data-url="<?= $page->url('subscribe.create') ?>">Да, хочу дружить</button>
		</div>
	</div>

	<div class="clear"></div>

	<div class="share-section clearfix">
		<div class="share-sectio__action">Посмотреть все <a href="/special_offers">wow-акции ></a></div>
		<noindex>
			<div class="share-sectio__link">Рассказать друзьям <ul class="clearfix"><li><a target="_blank" rel="nofollow" href="https://vk.com/youcanenter"></a></li><li><a target="_blank" rel="nofollow" class="fb" href="https://www.facebook.com/enter.ru"></a></li><li><a target="_blank" rel="nofollow" class="tw" href="https://twitter.com/enter_ru"></a></li></ul></div>
		</noindex>
	</div>
</div>