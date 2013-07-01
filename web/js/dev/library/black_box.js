/**
 * WARNING!
 *
 * @requires jQuery, simple_templating, pageConfig
 */


/**
 * Создает объект для обновления данных с сервера и отображения текущих покупок
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @param	{String}		updateUrl URL по которому будут запрашиватся данные о пользователе и корзине.
 * @param	{jQuery node}	mainNode  DOM элемент бокса
 * @constructor
 */
var BlackBox = function(updateUrl, mainContatiner){
	this.updUrl = (!docCookies.hasItem('enter') ||  !docCookies.hasItem('enter_auth')) ? updateUrl += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000) : updateUrl;
	this.mainNode = mainContatiner;
};

/**
 * Объект по работе с корзиной
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @return	{function} update	обновление данных о корзине
 * @return	{function} add		добавление в корзину
 */
BlackBox.prototype.basket = function() {
	var self = this;

	var headQ = $('#topBasket');
	var bottomQ = self.mainNode.find('.bBlackBox__eCartQuan');
	var bottomSum = self.mainNode.find('.bBlackBox__eCartSum');
	var total = self.mainNode.find('.bBlackBox__eCartTotal');
	var bottomCart = self.mainNode.find('.bBlackBox__eCart');
	var flyboxBasket = self.mainNode.find('.bBlackBox__eFlybox.mBasket');
	var flyboxInner = self.mainNode.find('.bBlackBox__eFlyboxInner');


	/**
	 * Уничтожение содержимого flybox и его скрытие
	 *
	 * @author	Zaytsev Alexandr
	 * @private
	 */
	var flyboxDestroy = function(){
		flyboxBasket.fadeOut(300, function(){
			flyboxInner.remove();
		});
	};

	/**
	 * Закрытие flybox по клику
	 * 
	 * @author	Zaytsev Alexandr
	 * @param	{Event} e
	 * @private
	 */
	var flyboxcloser = function(e){
		var targ = e.target.className;

		if (!(targ.indexOf('bBlackBox__eFlybox')+1) || !(targ.indexOf('fillup')+1)) {
			flyboxDestroy();
			$('body').unbind('click', flyboxcloser);
		}
	};

	/**
	 * Обновление данных о корзине
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{Object} basketInfo			Информация о корзине
	 * @param	{Number} basketInfo.cartQ	Количество товаров в корзине
	 * @param	{Number} basketInfo.cartSum	Стоимость товаров в корзине
	 * @public
	 */
	var update = function(basketInfo) {
		headQ.html('('+basketInfo.cartQ+')');
		bottomQ.html(basketInfo.cartQ);
		bottomSum.html(basketInfo.cartSum);
		bottomCart.addClass('mBought');
		total.show();
	};

	/**
	 * Добавление товара в корзину
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{Object} item
	 * @param	{String} item.title			Название товара
	 * @param	{Number} item.price			Стоимость товара
	 * @param	{String} item.imgSrc		Ссылка на изображение товара
	 * @param	{Number} item.TotalQuan		Общее количество товаров в корзине
	 * @param	{Number} item.totalSum		Общая стоимость корзины
	 * @param	{String} item.linkToOrder	Ссылка на оформление заказа
	 * @public
	 */
	var add = function(item) {
		var flyboxTmpl = tmpl('blackbox_basketshow_tmpl', item);

		flyboxDestroy();
		flyboxBasket.append(flyboxTmpl);
		flyboxBasket.show(300);

		var nowBasket = {
			cartQ: item.totalQuan,
			cartSum: item.totalSum
		};

		self.basket().update(nowBasket);

		$('body').bind('click', flyboxcloser);

	};

	return {
		'update': update,
		'add': add
	};
};

/**
 * Объект по работе с данными пользователя
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 * @return	{function} update
 */
BlackBox.prototype.user = function() {
	var self = this;

	/**
	 * Обновление пользователя
	 *
	 * @author	Zaytsev Alexandr
	 * @param	{String} userName Имя пользователя
	 * @public
	 */
	var update = function(userName) {
		var topAuth = $('#auth-link');
		var bottomAuth = self.mainNode.find('.bBlackBox__eUserLink');

		if (userName !== null) {
			var dtmpl={
				user: userName
			};
			var show_user = tmpl('auth_tmpl', dtmpl);

			topAuth.hide();
			topAuth.after(show_user);
			bottomAuth.html(userName).addClass('mAuth');
		}
		else {
			topAuth.show();
			
		}
	};

	return {
		'update': update
	};
};


/**
 * Инициализация BlackBox.
 * Получение данных о корзине и пользователе с сервера.
 *
 * @author	Zaytsev Alexandr
 * @this	{BlackBox}
 */
BlackBox.prototype.init = function() {
	var self = this;

	/**
	 * Обработчик Action присланных с сервера
	 * 
	 * @param	{Object} action Список действий которые необходимо выполнить
	 * @private
	 */
	var startAction = function(action) {
		if (action.subscribe !== undefined) {
			//  TODO: перевести action на события
			// lboxCheckSubscribe(action.subscribe);
		}
	};

	/**
	 * Обработчик данных о корзине и пользователе
	 * 
	 * @param	{Object} data
	 * @private
	 */
	var parseUserInfo = function(data) {
		if (data.success !== true) {
			return false;
		}

		var userInfo = data.user;
		var cartInfo = data.cart;
		var actionInfo = data.action;

		self.user().update(userInfo.name);

		if (cartInfo.quantity !== 0) {
			var nowBasket = {
				cartQ: cartInfo.quantity,
				cartSum: cartInfo.sum
			};
			self.basket().update(nowBasket);
		}

		if (actionInfo !== undefined) {
			startAction(actionInfo);
		}
	};

	$.get(self.updUrl, parseUserInfo);
};


/**
 * Создание и иницилизация объекта для работы с корзиной и данными пользователя
 * @type	{BlackBox}
 */
window.blackBox = new BlackBox(pageConfig.userUrl, $('.bBlackBox__eInner'));
blackBox.init();