;(function(){
	// текущая страница для каждой вкладки
	var reviewCurrentPage = {
		user: -1,
		pro: -1
	};
	// количество страниц для каждой вкладки
	var reviewPageCount = {
		user: 0,
		pro: 0
	};
	var reviewsProductId = null;
	var reviewsType = null;
	var reviewsContainerClass = null;

	//nodes
	var moreReviewsButton = $('.jsGetReviews');
	var reviewTab = $('.bReviewsTabs__eTab');
	var reviewWrap = $('.bReviewsWrapper');
	var reviewContent = $('.bReviewsContent');
	// получение отзывов
	var getReviews = function(productId, type, containerClass) {
		var page = reviewCurrentPage[type] + 1;
		
		var layout = false;
		if($('body').hasClass('jewel')) {
			layout = 'jewel';
		}

		$.get('/product-reviews/'+productId, {
			page: page,
			type: type,
			layout: layout
		}, 
		function(data){
			$('.'+containerClass).html($('.'+containerClass).html() + data.content);
			reviewCurrentPage[type]++;
			reviewPageCount[type] = data.pageCount;
			if(reviewCurrentPage[type] + 1 >= reviewPageCount[type]) {
				moreReviewsButton.hide();
			}
			else {
				moreReviewsButton.show();
			}
		});
	};

	// карточка товара - отзывы - переключение по табам
	if(reviewTab.length) {
		// начальная инициализация
		var initialType = reviewWrap.attr('data-reviews-type');

		reviewCurrentPage[initialType]++;
		reviewPageCount[initialType] = reviewWrap.attr('data-page-count');

		if(reviewPageCount[initialType] > 1) {
			moreReviewsButton.show();
		}
		reviewsProductId = reviewWrap.attr('data-product-id');
		reviewsType = reviewWrap.attr('data-reviews-type');
		reviewsContainerClass = reviewWrap.attr('data-container');

		reviewTab.click(function(){
			reviewsContainerClass = $(this).attr('data-container');
			if (reviewsContainerClass === undefined){
				return;
			}

			reviewsType = $(this).attr('data-reviews-type');
			reviewTab.removeClass('active');
			$(this).addClass('active');
			reviewContent.hide();
			$('.'+reviewsContainerClass).show();

			moreReviewsButton.hide();
			if (reviewsType === 'user') {
				moreReviewsButton.html('Показать ещё отзывы');
			} else if(reviewsType === 'pro') {
				moreReviewsButton.html('Показать ещё обзоры');
			}

			if(!$('.'+reviewsContainerClass).html()) {
				getReviews(reviewsProductId, reviewsType, reviewsContainerClass);
			} else {
				// проверяем что делать с кнопкой "показать еще" - скрыть/показать
				if(reviewCurrentPage[reviewsType] + 1 >= reviewPageCount[reviewsType]) {
					moreReviewsButton.hide();
				} else {
					moreReviewsButton.show();
				}
			}
		});

		moreReviewsButton.click(function(){
			getReviews(reviewsProductId, reviewsType, reviewsContainerClass);
		});
	}

	var leaveReview = function(){
		var productInfo = $('#jsProductCard').data('value');
		var pid = $(this).data('pid');
		var name = productInfo.name;
		var src = "http://reviews.testfreaks.com/reviews/new?client_id=enter.ru&" + $.param({key: pid, name: name});

		$(".reviewPopup").lightbox_me({
			onLoad: function() {
				$("#rframe").attr("src", src);
			}
		});
		return false;
	};

	$('.jsLeaveReview').on('click', leaveReview);

}());