/**
 * @author		Zaytsev Alexandr
 */
;$(function() {
	var $body = $('body'),
		template =
			'<div class="js-kitchenBuyButton-popup">' +
				'<a href="#" class="js-kitchenBuyButton-popup-close" title="Закрыть">Закрыть</a>' +

				'<form action="#" method="post">' +
					'{{#full}}' +
						'<h1>Закажите обратный звонок и уточните</h1>' +
						'<ul>' +
							'<li>Состав мебели и техники</li>' +
							'<li>Условия доставки, сборки и оплаты</li>' +
						'</ul>' +
					'{{/full}}' +

					'{{^full}}' +
						'<h1>Отравить заявку</h1>' +
					'{{/full}}' +

					'*Телефон: <input type="text" name="phone" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx" />' +
					'Имя: <input type="text" name="name" />' +
					'E-mail: <input type="text" name="email" placeholder="mail@domain.com" />' +
					'<p><label><input type="checkbox" name="confirm" value="1" /> Я ознакомлен и согласен с информацией о продавце и его офертой</label></p>' +
					'<p>Продавец-партнёр: {{partner}}</p>' +
					'{{#full}}' +
						'<p><a href="{{productUrl}}">Перейти в карточку товара</a></p>' +
					'{{/full}}' +
				'</form>' +
			'</div>';

	$body.on('click', '.js-kitchenBuyButton', function(e) {
		e.preventDefault();

		var $button = $(this);

		$(Mustache.render(template, {
			full: $button.data('full'),
			partner: $button.data('partner'),
			productUrl: $button.data('product-url')
		})).lightbox_me({
			centered: true,
			closeSelector: '.js-kitchenBuyButton-popup-close',
			destroyOnClose: true
		});

		$.mask.definitions['x'] = '[0-9]';
		$.mask.placeholder = "_";
		$.mask.autoclear = false;
		$.map($('.js-kitchenBuyButton-popup input'), function(elem, i) {
			if (typeof $(elem).data('mask') !== 'undefined') $(elem).mask($(elem).data('mask'));
		});
	});
});
