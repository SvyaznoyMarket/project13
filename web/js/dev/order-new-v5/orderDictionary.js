;(function (window, document, $, ENTER) {
	console.info('orderDictionary.js init...');

	var
		constructors = ENTER.constructors;
	// end of vars


	constructors.OrderDictionary = (function() {
		'use strict';

		/**
		 * Вспомагательная обертка вокруг данных приходящих с сервера
		 * Явлется неким драйвером для доступа к данным
		 *
		 * @author	Zaytsev Alexandr
		 * @this	{OrderDictionary}
		 *
		 * @param	{Object}	orderData	Данные о доставке
		 *
		 * @constructor
		 */
		function OrderDictionary( orderData ) {
			// enforces new
			if ( !(this instanceof OrderDictionary) ) {
				return new OrderDictionary(orderData);
			}
			// constructor body
			
			this.orderData = orderData;

			// alias
			this.serverTime = this.orderData.time;
			this.deliveryTypes = this.orderData.deliveryTypes;
			this.deliveryStates = this.orderData.deliveryStates;
			this.pointsByDelivery = this.orderData.pointsByDelivery;
			this.products = this.orderData.products;
			this.defPoints = this.orderData.defPoints || {};

            console.debug('OrderDictionary', this);
		}

		/**
		 * Получить имя метода доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{String}			Имя метода доставки
		 */
		OrderDictionary.prototype.getNameOfState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				console.warn('Не найден метод доставки '+state);
				
				return false;
			}

			return this.deliveryStates[state].name;
		};

		/**
		 * Получить таймштамп от которого нужно вести расчет
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @return	{Number}	Таймштамп
		 */
		OrderDictionary.prototype.getToday = function() {
			return this.serverTime;
		};

		/**
		 * Стандартная точка для метода доставки
		 * 
		 * @param token
		 * @returns {*}
		 */
		OrderDictionary.prototype.getDefaultPointId = function( token ) {
			return this.defPoints.hasOwnProperty(token) ? this.defPoints[token] : 0;
		};

		/**
		 * Есть ли метод доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Boolean}
		 */
		OrderDictionary.prototype.hasDeliveryState = function( state ) {
			return this.deliveryStates.hasOwnProperty(state);
		};


		/**
		 * Флаг уникальности для типа доставки state.
		 * Например, для типа доставки pickpoint должен быть false (задаётся в РНР-коде на сервере)
		 *
		 * @this        {OrderDictionary}
		 *
		 * @param       {String}    state    Метод доставки
		 * @returns     {Boolean}
		 */
		OrderDictionary.prototype.isUniqueDeliveryState = function ( state ) {
			var
				st;
			// end of vars
			
			if ( this.hasDeliveryState(state) ) {
				st = this.deliveryStates[state];

				return st['unique'];
			}

			return false;
		};

		/**
		 * Есть ли для метода доставки пункты доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Boolean}
		 */
		OrderDictionary.prototype.hasPointDelivery = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				return false;
			}

			return this.pointsByDelivery.hasOwnProperty(state);
		};

		/**
		 * Получить данные о точке доставки по методу доставки и идетификатору точки доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state		Метод доставки
		 * @param	{String}	pointId		Идентификатор точки доставки
		 * @return	{Object}				Данные о точке доставки
		 */
		OrderDictionary.prototype.getPointByStateAndId = function( state, pointId ) {
			var
				points = this.getAllPointsByState(state),
				i;
			// end of vars
			
			pointId = pointId+'';
			
			for ( i = points.length - 1; i >= 0; i-- ) {
				if ( points[i].id === pointId ) {
					return window.ENTER.utils.cloneObject(points[i]);
				}
			}

			return false;
		};

		/**
		 * Получение первой точки доставки для метода доставки
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state		Метод доставки
		 * @return	{Object}				Данные о точке доставки
		 */
		OrderDictionary.prototype.getFirstPointByState = function( state ) {
			var
				points = this.getAllPointsByState(state);
			// end of vars
			return ( points[0] ) ? ENTER.utils.cloneObject(points[0]) : false;
		};

		/**
		 * @this	{OrderDictionary}
		 *
		 * @param	{String}	state	Метод доставки
		 */
		OrderDictionary.prototype.getAllPointsByState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				return false;
			}

			var
				point = this.pointsByDelivery[state],
				pointName = point ? point.token : false,
				ret = pointName ? this.orderData[pointName] : false,
				retNew = [], i, type;
			// end of vars

			/*
			 SITE-2499 Некорректный первоначальный список магазинов при оформлении заказа
			 Фильтруем точки для типов доставки "now" и "self"
			 */
			if ( state == "now" || state == "self" || state == 'self_svyaznoy') {
				for ( i in ret ) {
					for ( type in ret[i].products ) {
						type == state && retNew.push(ret[i]);
					}
				}

				ret = retNew;
			}

			return ret || false;
		};


		OrderDictionary.prototype.getChangeButtonText = function( state ) {
			var
				text = ( this.pointsByDelivery[state] ) ? this.pointsByDelivery[state].changeName : 'Сменить';
			// end of vars
			
			return text;
		};


		/**
		 * Получить спискок продуктов для которых доступен данный метод доставки
		 * 
		 * @this	{OrderDictionary}
		 * 
		 * @param	{String}	state	Метод доставки
		 * @return	{Array}				Массив идентификаторов продуктов
		 */
		OrderDictionary.prototype.getProductFromState = function( state ) {
			if ( !this.hasDeliveryState(state) ) {
				console.warn('Не найден метод доставки '+state);

				return false;
			}

			return this.deliveryStates[state].products;
		};

		/**
		 * Получить данные о продукте по идентификатору продукта
		 *
		 * @this	{OrderDictionary}
		 * 
		 * @param	{Number}	productId	Идентификатор продукта
		 * @return	{Object}				Данные о продукте
		 */
		OrderDictionary.prototype.getProductById = function( productId ) {
			if ( !this.products.hasOwnProperty(productId) ) {
				console.warn('Такого продукта не найдено');

				return false;
			}

			return this.products[productId];
		};

		/**
		 * Метод ищет у продукта любую возможность забрать из конкретной точки доставки
		 * Если метод доставки находится - возвращается метод доставки,
		 * Если нет - возвращатеся false
		 *
		 * @param	{Number}	productId		Идентификатор продукта
		 * @param	{Number}	pointId			Идентификатор точки доставки
		 */
		OrderDictionary.prototype.getStateToProductByDeliveryID = function( productId, pointId ) {
			console.info('Перебираем все методы доставок и ищем среди них со схожим типом точек доставок');
			var
				productDeliveries = this.products[productId].deliveries,
				deliveriesType;
			// end of vars

			console.log('productId ' + productId);
			console.log('pointId ' + pointId);
			console.log(productDeliveries);

			for ( deliveriesType in productDeliveries ) {
				console.log('ищем в ' + deliveriesType);
				if ( productDeliveries.hasOwnProperty(deliveriesType) && productDeliveries[deliveriesType].hasOwnProperty(pointId) ) {
					console.log('возвращаем ' + deliveriesType);
					return deliveriesType;
				}
			}

			console.warn('не нашли...((');
			return false;
		};


		return OrderDictionary;
	
	}());
	
}(this, this.document, this.jQuery, this.ENTER));