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
		//clientUserInfo = utils.extendApp('ENTER.config.userInfo'),
		body = $('body'),
		dCook = window.docCookies,
		authorized_cookie = '_authorized';
	// end of vars
	
	
	clientCart.products = [];
	config.userInfo = null;


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

					console.log('basketInfo', basketInfo)

					ENTER.UserModel.cartProductQuantity(basketInfo.quantity);
					clientCart.totalQuan = basketInfo.sum;

					body.trigger('basketUpdate', [basketInfo]);

					// запуск маркировки кнопок «купить»
					body.trigger('markcartbutton');
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

					console.log('BlackBox add');
					ENTER.UserModel.cart.unshift(data.product);

				},

                multipleAdd = function multipleAdd ( data ) {

                    var cart = data.cart,
                        toClientCart = {},
                        toBasketUpdate = {
                            quantity: cart.full_quantity,
                            sum: cart.full_price
                        };

                    for (var i = 0, len = data.products.length; i < len; i++) {
                        var product = data.products[i],
                            tmpCart = {
                                formattedPrice: printPrice(product.price),
                                image: product.img,
                                url: product.link
                            };
                        toClientCart = $.extend({}, product, tmpCart);

                        var productInBasket = $.grep(clientCart.products, function(elem){ return elem.id === product.id });
                        if (productInBasket.length == 0) {
                            clientCart.products.push(toClientCart); // добавляем в корзину только уникальные элементы
                        } else {
                            for (var a in clientCart.products) if (clientCart.products[a].id === product.id) clientCart.products[a].quantity = product.quantity; // обновляем количество для существующих
                        }
                    }

                    self.basket().update(toBasketUpdate);
                },

				deleteItem = function deleteItem( data ) {
					console.log('deleteItem', data);
				};
			//end of functions


			return {
				'update': update,
				'add': add,
                'multipleAdd' : multipleAdd,
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

					if ( !dCook.hasItem(authorized_cookie) ) {
						if ( userInfo && null !== userInfo.id ) {
							console.log('update userInfo: enable authorized_cookie');
							dCook.setItem(authorized_cookie, 1, 60*60, '/'); // on
						}
						else {
							console.log('update userInfo: disable authorized_cookie');
							dCook.setItem(authorized_cookie, 0, 60*60, '/'); // off
						}
					}

					body.trigger('userLogged', [userInfo]);
				};
			

			return {
				'update': update
			};
		};

		BlackBox.prototype.init = function() {
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

}(window.ENTER));