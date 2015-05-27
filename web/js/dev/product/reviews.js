;(function() {
	// текущая страница для каждой вкладки
	var reviewCurrentPage = {
			user: -1,
			pro: -1
		},
		// количество страниц для каждой вкладки
		reviewPageCount = {
			user: 0,
			pro: 0
		},
		reviewsProductUi = null,
		reviewsType = null,
		reviewsContainerClass = null,

		//nodes
		moreReviewsButton = $('.jsGetReviews'),
		reviewTab = $('.bReviewsTabs__eTab'),
		reviewWrap = $('.bReviewsWrapper'),
		reviewContent = $('.bReviewsContent');
	// end of vars

	/**
	 * Получение отзывов
	 * @param	{String}	productId
	 * @param	{String}	type
	 * @param	{String}	containerClass
	 */
	var getReviews = function( productId, type, containerClass ) {
		var page = reviewCurrentPage[type] + 1,
			layout = false,
			url = '/product-reviews/'+productId,
			dataToSend;
		// end of vars

		var reviewsResponse = function reviewsResponse( data ) {
            var $container = $('.'+containerClass);
			$container.html($container.html() + data.content);
			reviewCurrentPage[type]++;
			reviewPageCount[type] = data['pageCount'];

			if ( reviewCurrentPage[type] + 1 >= reviewPageCount[type] ) {
				moreReviewsButton.hide();
			}
			else {
				moreReviewsButton.show();
			}
		};
		// end of functions

		if ( $('body').hasClass('jewel') ) {
			layout = 'jewel';
		}

		dataToSend = {
			page: page,
			type: type,
			layout: layout
		};

		$.ajax({
			type: 'GET',
			data: dataToSend,
			url: url,
			success: reviewsResponse
		});
	};

	// карточка товара - отзывы - переключение по табам
	if ( reviewTab.length ) {
		// начальная инициализация
		var initialType = reviewWrap.attr('data-reviews-type');

		reviewCurrentPage[initialType]++;
		reviewPageCount[initialType] = reviewWrap.attr('data-page-count');

		if ( reviewPageCount[initialType] > 1 ) {
			moreReviewsButton.show();
		}

		reviewsProductUi = reviewWrap.attr('data-product-ui');
		reviewsType = reviewWrap.attr('data-reviews-type');
		reviewsContainerClass = reviewWrap.attr('data-container');

		reviewTab.click(function() {

            var $reviewContainer = $('.'+reviewsContainerClass);

			reviewsContainerClass = $(this).attr('data-container');

			if ( reviewsContainerClass === undefined ) {
				return;
			}

			reviewsType = $(this).attr('data-reviews-type');
			reviewTab.removeClass('active');
			$(this).addClass('active');
			reviewContent.hide();
			$reviewContainer.show();

			moreReviewsButton.hide();

			if (reviewsType === 'user') {
				moreReviewsButton.html('Показать ещё отзывы');
			}

			if ( !$reviewContainer.html() ) {
				getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
			}
			else {
				// проверяем что делать с кнопкой "показать еще" - скрыть/показать
				if ( reviewCurrentPage[reviewsType] + 1 >= reviewPageCount[reviewsType] ) {
					moreReviewsButton.hide();
				}
				else {
					moreReviewsButton.show();
				}
			}
		});

		moreReviewsButton.click(function() {
			getReviews(reviewsProductUi, reviewsType, reviewsContainerClass);
		});
	}

//	var leaveReview = function() {
//		if ( !$('#jsProductCard').length ) {
//			return false;
//		}
//
//		var productInfo = $('#jsProductCard').data('value'),
//			pid = $(this).data('pid'),
//			name = productInfo.name,
//			src = 'http://reviews.testfreaks.com/reviews/new?client_id=enter.ru&' + $.param({key: pid, name: name});
//		// end of vars
//
//		$('.reviewPopup').lightbox_me({
//			onLoad: function() {
//				$('#rframe').attr('src', src);
//			}
//		});
//
//		return false;
//	};
//
//	$('.jsLeaveReview').on('click', leaveReview);

}(jQuery));



/**
 * Обработчик для формы "Отзыв о товаре"
 *
 * @author		Shaposhnik Vitaly
 */
(function() {
	var body = $('body'),
		reviewPopup = $('.jsReviewPopup'),
		form = reviewPopup.find('.jsReviewForm'),
		submitReviewButton = $('.jsFormSubmit'),
		submitReviewButtonText = submitReviewButton.val(),

		reviewStar = form.find('.starsList__item'),
		reviewStarCount = form.find('.jsReviewStarsCount'),
		starStateClass = {
			fill: 'mFill',
			empty: 'mEmpty'
		},

		advantageField = $('.jsAdvantage'),
		disadvantageField = $('.jsDisadvantage'),
		extractField = $('.jsExtract'),
		authorNameField = $('.jsAuthorName'),
		authorEmailField = $('.jsAuthorEmail'),

		/**
		 * Конфигурация валидатора для формы "Отзыв о товаре"
		 *
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: advantageField,
					require: true,
					customErr: 'Не указаны достоинства'
				},
				{
					fieldNode: disadvantageField,
					require: true,
					customErr: 'Не указаны недостатки'
				},
				{
					fieldNode: extractField,
					require: true,
					customErr: 'Не указан комментарий'
				},
				{
					fieldNode: authorNameField,
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: authorEmailField,
					require: true,
					customErr: 'Не указан e-mail',
					validBy: 'isEmail'
				}
			]
		},
		validator = new FormValidator(validationConfig);
	//end of vars

	var 
		/**
		 * Открытие окна с отзывами
		 */
		openPopup = function openPopup() {
			reviewPopup.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {}
			});

			return false;
		},

		/**
		 * Обработка ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			var field = $('[name="review[' + formError.field + ']"]');
			// end of vars

			var clearError = function clearError() {
				validator._unmarkFieldError($(this));
			};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		},

		/**
		 * Показ глобальных сообщений об ошибках
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 */
		showError = function showError( msg ) {
			var error = $('ul.error_list', form);
			// end of vars

			if ( error.length ) {
				error.html('<li>' + msg + '</li>');
			}
			else {
				$('.bFormLogin__ePlaceTitle', form).after($('<ul class="error_list" />').append('<li>' + msg + '</li>'));
				$( form ).prepend( $('<ul class="error_list" />').append('<li>' + msg + '</li>') );
			}

			return false;
		},

		/**
		 * Обработка ошибок из ответа сервера
		 *
		 * @param {Object} res Ответ сервера
		 */
		serverErrorHandler = function serverErrorHandler( res ) {
			var formError = null;
			// end of vars

			console.warn('Обработка ошибок формы');

			for ( var i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];
				console.warn(formError);

				if ( formError.field !== 'global' && formError.message !== null ) {
					formErrorHandler(formError);
				}
				else if ( formError.field === 'global' && formError.message !== null ) {
					showError(formError.message);
				}
			}

			return false;
		},

		/**
		 * Обработчик ответа от сервера
		 *
		 * @param	{Object}	response	Ответ сервера
		 */
		responseFromServer = function responseFromServer( response ) {
			console.log('Ответ от сервера');

			if ( response.error ) {
				console.warn('Form has error');
				serverErrorHandler(response);

				return false;
			}

			if ( response.success ) {
				if (response.notice.message) {
					form.before(response.notice.message);
				}
				form.hide();
			}

			return false;
		},

		/**
		 * Сабмит формы "Отзыв о товаре"
		 */
		formSubmit = function formSubmit() {
			if (form.data('disabled')) {
				return false;
			}

			form.data('disabled', true);
			submitReviewButton.attr('disabled', 'disabled');
			submitReviewButton.addClass('mDisabled');
			submitReviewButton.val('Сохраняю…');

			// очищаем блок с глобальными ошибками
			if ( $('ul.error_list', form).length ) {
				$('ul.error_list', form).html('');
			}

			function enableSubmitReviewButton() {
				form.data('disabled', false);
				submitReviewButton.removeAttr('disabled');
				submitReviewButton.removeClass('mDisabled');
				submitReviewButton.val(submitReviewButtonText);
			}

			validator.validate({
				onInvalid: function( err ) {
					console.warn('invalid');
					console.log(err);

					enableSubmitReviewButton();
				},
				onValid: function() {
					$.ajax({
						type: 'post',
						url: form.attr('action'),
						data: form.serializeArray(),
						dataType: 'json',
						success: responseFromServer,
						complete: function() {
							enableSubmitReviewButton();
						}
					});
					console.log('Сабмит формы "Отзыв о товаре"');

					return false;
				}
			});

			return false;
		},

		/**
		 * Закрашивание необходимого количества звезд
		 * 
		 * @param	{Number}	count	Количество звезд которое необходимо закрасить
		 */
		fillStars = function fillStars( count ) {
			reviewStar.removeClass(starStateClass['fill']).removeClass(starStateClass['empty']);

			reviewStar.each(function( index ) {
				if ( index + 1 <= count ) {
					$(this).addClass(starStateClass['fill']);
				}
				else {
					$(this).addClass(starStateClass['empty']);
				}
			});
		},

		/**
		 * Наведение на звезду курсора
		 */
		hoverStar = function hoverStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars

			fillStars(starIndex);			
		},

		/**
		 * Событие сведения курсора со звезды
		 */
		unhoverStar = function unhoverStar() {
			var nowStarCount = reviewStarCount.val();
			// end of vars
			
			fillStars(nowStarCount);
		},

		/**
		 * Нажатие на звезду
		 */
		markStar = function markStar() {
			var nowStar = $(this),
				starIndex = nowStar.index() + 1;
			// end of vars
			
			reviewStarCount.val(starIndex);
			fillStars(starIndex);
		},

		/**
		 * Заполнение данных пользователя в форме (поля "Ваше имя" и "Ваш e-mail") и скрытие полей.
		 *
		 * @param  {Object} userInfo
		 */
		fillUserData = function fillUserData( userInfo ) {
			if ( userInfo ) {
				// если присутствует имя пользователя
				if ( userInfo.name ) {
					authorNameField.val(userInfo.name);
					authorNameField.parent('.jsPlace2Col').hide();
				}
				// если присутствует email пользователя
				if ( userInfo.email ) {
					authorEmailField.val(userInfo.email);
					authorEmailField.parent('.jsPlace2Col').hide();
				}
				// если присутствует и имя и email пользователя, то скрываем весь fieldset
				if ( userInfo.name && userInfo.email ) {
					authorNameField.parents('.jsFormFieldset').hide();
				}
			}
		};
	//end of functions


	body.on('click', '.jsReviewSend', openPopup);
	body.on('submit', '.jsReviewForm', formSubmit);
	fillUserData(ENTER.config.userInfo.user);

	reviewStar.hover(hoverStar, unhoverStar);
	reviewStar.on('unhover', unhoverStar);
	reviewStar.on('click', markStar);

    if ('#add-review' == location.hash) {
        openPopup();
    }
}());
