/**
 * Механика работы с корзиной и данными пользователя
 * Генерирует события и распределяет данные между функциями
 * 
 * @requires jQuery, docCookies, ENTER.utils, ENTER.config
 * 
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var
		config = ENTER.config,
		userUrl = config.pageConfig.userUrl,
		utils = ENTER.utils,
		clientCart = utils.extendApp('ENTER.config.clientCart'),
		clientUserInfo = utils.extendApp('ENTER.config.userInfo'),
		body = $('body'),
		authorized_cookie = '_authorized';
	// end of vars
	
	
	clientCart.products = [];


	/**
	 * === BLACKBOX CONSTRUCTOR ===
	 */
	var BlackBox = (function() {
	
		/**
		 * Создает объект для обновления данных с сервера и отображения текущих покупок
		 *
		 * @this	{BlackBox}
		 * 
		 * @param	{String}		updateUrl	URL по которому будут запрашиватся данные о пользователе и корзине.
		 * @param	{Object}		mainNode	DOM элемент бокса
		 * 
		 * @constructor
		 */
		function BlackBox( updateUrl ) {
			// enforces new
			if ( !(this instanceof BlackBox) ) {
				return new BlackBox(updateUrl);
			}
			// constructor body

			this.updUrl = ( !window.docCookies.hasItem('enter') || !window.docCookies.hasItem('enter_auth') ) ? updateUrl += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000) : updateUrl;
		}

		
		/**
		 * Объект по работе с корзиной
		 * 
		 * @this	{BlackBox}
		 * 
		 * @return	{Function} update	обновление данных о корзине
		 * @return	{Function} add		добавление в корзину
		 */
		BlackBox.prototype.basket = function() {
			var
				self = this;
			// end of vars

				
			var
				/**
				 * Обновление данных о корзине
				 * 
				 * @param	{Object}	basketInfo			Информация о корзине
				 * @param	{Number}	basketInfo.cartQ	Количество товаров в корзине
				 * @param	{Number}	basketInfo.cartSum	Стоимость товаров в корзине
				 * 
				 * @public
				 */
				update = function update( basketInfo ) {
					clientCart.totalSum = basketInfo.quantity;
					clientCart.totalQuan = basketInfo.sum;

					body.trigger('basketUpdate', [basketInfo]);

					// запуск маркировки кнопок «купить»
					body.trigger('markcartbutton');
					// запуск маркировки спиннеров
					body.trigger('updatespinner');
				},

				/**
				 * Добавление товара в корзину
				 * 
				 * @param	{Object}	item
				 * @param	{String}	item.title			Название товара
				 * @param	{Number}	item.price			Стоимость товара
				 * @param	{String}	item.imgSrc			Ссылка на изображение товара
				 * @param	{Number}	item.TotalQuan		Общее количество товаров в корзине
				 * @param	{Number}	item.totalSum		Общая стоимость корзины
				 * @param	{String}	item.linkToOrder	Ссылка на оформление заказа
				 * 
				 * @public
				 */
				add = function add ( data ) {
					var product = data.product,
						cart = data.cart,
						tmpCart = {
							formattedPrice: printPrice(product.price),
							image: product.img,
							url: product.link
						},
						toClientCart = {},
						toBasketUpdate = {
							quantity: cart.full_quantity,
							sum: cart.full_price
						};
					// end of vars

					toClientCart = $.extend(
							{},
							product,
							tmpCart);

					clientCart.products.push(toClientCart);
					self.basket().update(toBasketUpdate);
					// body.trigger('productAdded');

				},

				deleteItem = function deleteItem( data ) {
					console.log('deleteItem');
					var
						deleteItemId = data.product.id,
						toBasketUpdate = {
							quantity: data.cart.full_quantity,
							sum: data.cart.full_price
						},
						i;
					// end of vars
					
					for ( i = clientCart.products.length - 1; i >= 0; i-- ) {
						if ( clientCart.products[i].id === deleteItemId ) {
							clientCart.products.splice(i, 1);

							self.basket().update(toBasketUpdate);

							return;
						}
					}

				};
			//end of functions


			return {
				'update': update,
				'add': add,
				'deleteItem': deleteItem
			};
		};


		/**
		 * Объект по работе с данными пользователя
		 * 
		 * @this	{BlackBox}
		 * 
		 * @return	{Function}	update
		 */
		BlackBox.prototype.user = function() {
			var 
				self = this;
			// end of vars


			var
				/**
				 * Обновление пользователя
				 * 
				 * @param	{String}	userInfo	Данные пользователя
				 * 
				 * @public
				 */
				update = function update ( userInfo ) {
					console.info('blackBox update userinfo');

					config.userInfo = userInfo;

					body.trigger('userLogged', [userInfo]);
				};
			

			return {
				'update': update
			};
		};


		/**
		 * Инициализация BlackBox.
		 * Получение данных о корзине и пользователе с сервера.
		 * 
		 * @this	{BlackBox}
		 */
		BlackBox.prototype.init = function() {
			var
				self = this;
			// end of vars


			var
				/**
				 * Обработчик Action присланных с сервера
				 * 
				 * @param	{Object}	action	Список действий которые необходимо выполнить
				 * 
				 * @private
				 */
				/*startAction = function startAction( action ) {
				},*/

				/**
				 * Обработчик данных о корзине и пользователе
				 * 
				 * @param	{Object}	data
				 * 
				 * @private
				 */ 
				parseData = function parseData( data ) {
					var
						userInfo = data.user,
						cartInfo = data.cart,
						productsInfo = data.cartProducts,
						actionInfo = data.action;
					//end of vars
					

					if ( data.success !== true ) {
						return false;
					}

					self.user().update(userInfo);

					if ( cartInfo.quantity && productsInfo.length ) {
						clientCart.products = productsInfo;
						self.basket().update( cartInfo );
					}

					/*if ( actionInfo !== undefined ) {
						startAction(actionInfo);
					}*/
				};
			//end of functions

			$.get(self.updUrl, parseData);
		};

	
		return BlackBox;
	
	}());
	/**
	 * === END BLACKBOX CONSTRUCTOR ===
	 */


	/**
	 * Создание и иницилизация объекта для работы с корзиной и данными пользователя
	 * 
	 * @type	{BlackBox}
	 */
	utils.blackBox = new BlackBox(userUrl);
	console.log('utils.blackBox created');
	if ( !window.docCookies.hasItem(authorized_cookie) || 1 === window.docCookies.getItem(authorized_cookie) ) {
		utils.blackBox.init();
		console.log('utils.blackBox init');
	}

}(window.ENTER));