/**
 * @requires jQuery, simple_templating, docCookies, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var config = ENTER.config,
		userUrl = config.pageConfig.userUrl,
		utils = ENTER.utils;
	// end of vars

    config.cartProducts = config.cartProducts || {};

	/**
	 * === BLACKBOX CONSTRUCTOR ===
	 */
	var BlackBox = (function() {
	
		/**
		 * Создает объект для обновления данных с сервера и отображения текущих покупок
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{BlackBox}
		 * 
		 * @param	{String}		updateUrl	URL по которому будут запрашиватся данные о пользователе и корзине.
		 * @param	{Object}		mainNode	DOM элемент бокса
		 * 
		 * @constructor
		 */
		function BlackBox( updateUrl, mainContatiner ) {
			// enforces new
			if ( !(this instanceof BlackBox) ) {
				return new BlackBox(updateUrl, mainContatiner);
			}
			// constructor body
			
			this.updUrl = ( !window.docCookies.hasItem('enter') || !window.docCookies.hasItem('enter_auth') ) ? updateUrl += '?ts=' + new Date().getTime() + Math.floor(Math.random() * 1000) : updateUrl;
			this.mainNode = mainContatiner;
		}
	
		
		/**
		 * Объект по работе с корзиной
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{BlackBox}
		 * 
		 * @return	{Function} update	обновление данных о корзине
		 * @return	{Function} add		добавление в корзину
		 */
		BlackBox.prototype.basket = function() {
			var self = this,

				headQ = $('#topBasket'),
				bottomQ = self.mainNode.find('.bBlackBox__eCartQuan'),
				bottomSum = self.mainNode.find('.bBlackBox__eCartSum'),
				total = self.mainNode.find('.bBlackBox__eCartTotal'),
				bottomCart = self.mainNode.find('.bBlackBox__eCart'),
				flyboxBasket = self.mainNode.find('.bBlackBox__eFlybox.mBasket'),
				flyboxInner = self.mainNode.find('.bBlackBox__eFlyboxInner');
			// end of vars

				/**
				 * Уничтожение содержимого flybox и его скрытие
				 *
				 * @author	Zaytsev Alexandr
				 * 
				 * @private
				 */
			var flyboxDestroy = function flyboxDestroy() {
					flyboxBasket.hide(0, function() {
						flyboxInner.remove();
					});
				},

				/**
				 * Закрытие flybox по клику
				 * 
				 * @author	Zaytsev Alexandr
				 * 
				 * @param	{Event}	e
				 * 
				 * @private
				 */
				flyboxcloser = function flyboxcloser( e ) {
					var targ = e.target.className;

					if ( !(targ.indexOf('bBlackBox__eFlybox') + 1) || !(targ.indexOf('fillup') + 1) ) {
						flyboxDestroy();
						$('body').unbind('click', flyboxcloser);
					}
				},

				/**
				 * Обновление данных о корзине
				 *
				 * @author	Zaytsev Alexandr
				 * 
				 * @param	{Object}	basketInfo			Информация о корзине
				 * @param	{Number}	basketInfo.cartQ	Количество товаров в корзине
				 * @param	{Number}	basketInfo.cartSum	Стоимость товаров в корзине
				 * 
				 * @public
				 */
				update = function update( basketInfo, cartProds ) {
					headQ.html('(' + basketInfo.cartQ + ')');
					bottomQ.html(basketInfo.cartQ);
					bottomSum.html(basketInfo.cartSum);
					bottomCart.addClass('mBought');
					total.show();

                    if ( cartProds && cartProds.length > 0 ) {
                        config.cartProducts = cartProds;
                    }
				},

				/**
				 * Добавление товара в корзину
				 *
				 * @author	Zaytsev Alexandr
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
				add = function add ( item ) {
					var flyboxTmpl = tmpl('blackbox_basketshow_tmpl', item),
                        nowBasket = {
                            cartQ: item.totalQuan,
                            cartSum: item.totalSum
                        },
                        addCartProduct = {
                            id: item.id,
                            name: item.title,
                            price: item.priceInt,
                            quantity: item.quantity
                        };
					// end of vars

					flyboxDestroy();
					flyboxBasket.append(flyboxTmpl);
					flyboxBasket.show(300);

					self.basket().update(nowBasket);
                    config.cartProducts.push(addCartProduct);

					$('body').bind('click', flyboxcloser);

				};
			//end of functions

			return {
				'update': update,
				'add': add
			};
		};

		/**
		 * Объект по работе с данными пользователя
		 *
		 * @author	Zaytsev Alexandr
		 * 
		 * @this	{BlackBox}
		 * 
		 * @return	{Function}	update
		 */
		BlackBox.prototype.user = function() {
			var self = this;

			/**
			 * Обновление пользователя
			 *
			 * @author	Zaytsev Alexandr
			 * 
			 * @param	{String}	userInfo	Данные пользователя
			 * 
			 * @public
			 */
			var update = function update ( userInfo ) {
				var topAuth = $('#auth-link'),
					bottomAuth = self.mainNode.find('.bBlackBox__eUserLink'),
					dtmpl = {},
					show_user = '';
				//end of vars

				config.userInfo = userInfo;

				if ( userInfo && userInfo.name !== null ) {
					dtmpl = {
						user: userInfo.name
					};

					show_user = tmpl('auth_tmpl', dtmpl);
					
					if ( topAuth.length ) {
						topAuth.hide();
						topAuth.after(show_user);
					}

					if ( bottomAuth.length ) {
						bottomAuth.html(userInfo.name).addClass('mAuth');
					}
				}
				else {
					topAuth.show();

				}

				$('body').trigger('userLogged', [userInfo]);
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
		 * 
		 * @this	{BlackBox}
		 */
		BlackBox.prototype.init = function() {
			var self = this;

				/**
				 * Обработчик Action присланных с сервера
				 * 
				 * @param	{Object}	action	Список действий которые необходимо выполнить
				 * 
				 * @private
				 */
			var startAction = function startAction( action ) {
					if ( action.subscribe !== undefined ) {
						$('body').trigger('showsubscribe', [action.subscribe]);
					}
					if ( action.cartButton !== undefined ) {
						$('body').trigger('markcartbutton', [action.cartButton]);
						$('body').trigger('updatespinner', [action.cartButton]);
					}
				},

				/**
				 * Обработчик данных о корзине и пользователе
				 * 
				 * @param	{Object}	data
				 * 
				 * @private
				 */ 
				parseUserInfo = function parseUserInfo( data ) {
					var userInfo = data.user,
						cartInfo = data.cart,
						actionInfo = data.action,
						nowBasket = {};
					//end of vars

					if ( data.success !== true ) {
						return false;
					}

					self.user().update(userInfo);

					if ( cartInfo.quantity !== 0 ) {
						nowBasket = {
							cartQ: cartInfo.quantity,
							cartSum: cartInfo.sum
						};

						self.basket().update( nowBasket, data.cartProducts );
					}

					if ( actionInfo !== undefined ) {
						startAction(actionInfo);
					}
				};
			//end of functions

			$.get(self.updUrl, parseUserInfo);
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
	utils.blackBox = new BlackBox(userUrl, $('.bBlackBox__eInner'));
	utils.blackBox.init();
	
}(window.ENTER));