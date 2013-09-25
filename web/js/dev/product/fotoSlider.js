/**
 * Слайдер изображений товара
 *
 * @author	Zaytsev Alexandr
 * @requires jQuery
 */
;(function(){

	/**
	 * Инициализация слайдера
	 *
	 * @param	{Object}	slider		Элемент слайдера
	 * @param	{Object}	fotoBox		Элемент контейнера с фотографиями
	 * @param	{Object}	leftArr		Стрелка влево
	 * @param	{Object}	rightArr	Стрелка вправо
	 * @param	{Object}	photos		Карточки фотографий
	 * @param	{Number}	itemW		Ширина одной карточки с фотографией
	 * @param	{Number}	nowLeft		Текущий отступ слева
	 */
	var initFotoSlider = function(){
		var slider = $('.bPhotoSlider');
		var fotoBox = slider.find('.bPhotoSliderGallery');
		var leftArr = slider.find('.bPhotoSlider__eBtn.mPrev');
		var rightArr = slider.find('.bPhotoSlider__eBtn.mNext');
		var photos = fotoBox.find('.bPhotoSliderGallery__eItem');

		if (!photos.length){
			return false;
		}

		var itemW = photos.width() + parseInt(photos.css('marginLeft'),10) + parseInt(photos.css('marginRight'),10);
		var nowLeft = 0;

		fotoBox.css({'width': photos.length*itemW, 'left':nowLeft});
		/**
		 * Проверка стрелок
		 */
		var checkArrow = function(){
			if (nowLeft > 0){
				leftArr.show();
			}
			else {
				leftArr.hide();	
			}

			if (nowLeft < fotoBox.width()-slider.width()){
				rightArr.show();
			}
			else {
				rightArr.hide();
			}
		};

		/**
		 * Предыдущее фото
		 */
		var prevFoto = function(){
			nowLeft = nowLeft - itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		/**
		 * Следущее фото
		 */
		var nextFoto = function(){
			nowLeft = nowLeft + itemW;
			fotoBox.animate({'left':-nowLeft});
			checkArrow();
			return false;
		};

		checkArrow();

		leftArr.bind('click', prevFoto);
		rightArr.bind('click', nextFoto);
	};

	$(document).ready(function() {
		if ( $('.bPhotoSlider').length){
			initFotoSlider();
		}
	});
})();