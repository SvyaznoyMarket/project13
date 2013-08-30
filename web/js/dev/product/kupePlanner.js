/**
 * Планировщик шкафов купе
 *
 * @requires jQuery
 */
;(function(global) {
	/**
	 * Имя объекта для конструктора шкафов купе
	 *
	 * ВНИМАНИЕ
	 * Имя переменной менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Переменная должна находится в глобальной области видимости
	 */
	global.Planner3dKupeConstructor = null;


	/**
	 * Callback Инициализации конструктора шкафов
	 *
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_Init = function ( ApiIds ) {
		// console.info(ApiIds)
	};


	/**
	 * Callback изменений в конструкторе шкафов
	 * 
	 * ВНИМАНИЕ
	 * Название функции менять нельзя. Захардкожено в файле KupeConstructorScript.js
	 * Функция должна находится в глобальной области видимости
	 */
	global.Planner3d_UpdatePrice = function ( IdsWithInfo ) {
		var url = $('#planner3D').data('cart-sum-url'),
			product = {};
		// end of vars

		product.product = {};

		var authFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsPrice').html(printPrice(res.sum));
		};

		for ( var i = 0, len = IdsWithInfo.length; i < len; i++ ) {
			var prodID = IdsWithInfo[i].id;

			if ( IdsWithInfo[i].error !== '' ) {
				$('.jsBuyButton').addClass('mDisabled');
				$('#coupeError').html('Вставки продаются только парами!').show();

				return false;
			}

			$('.jsBuyButton').removeClass('mDisabled');
			$('#coupeError').hide();

			if ( product.product[prodID+''] !== undefined ) {
				product.product[prodID+''].quantity++;
			}
			else {
				product.product[prodID+''] = {
					id : prodID,
					quantity : 1
				};
			}
		}

		$.ajax({
			type: 'POST',
			url: url,
			data: product,
			success: authFromServer
		});
	};


	/**
	 * Добавление шкафа купе в корзину
	 */
	var kupe2basket = function() {
		if ( $(this).hasClass('mDisabled') ) {
			return false;
		}

		var structure = global.Planner3dKupeConstructor.GetBasketContent();
		var url = $(this).attr('href');

		var resFromServer = function( res ) {
			if ( !res.success ) {
				return false;
			}

			$('.jsBuyButton').html('В корзине').addClass('mBought').attr('href','/cart');

			/* костыль */
			res.product.name = $('.bMainContainer__eHeader-title').html();
			res.product.price = $('.jsPrice').eq('1').html();
			res.product.article = $('.bMainContainer__eHeader-article').html();
			/* */
			
			$('body').trigger('addtocart', [res]);
		};

		var product = {};

		product.product = structure;

		$.ajax({
			type: 'POST',
			url: url,
			data: product,
			success: resFromServer
		});

		return false;
	};

	var initPlanner = function() {
		try {
			var coupeInfo = $('#planner3D').data('product');
			
			global.Planner3dKupeConstructor = new DKupe3dConstructor(document.getElementById('planner3D'),'/css/item/coupe_img/','/css/item/coupe_tex/', '/css/item/test_coupe_icons/');
			global.Planner3dKupeConstructor.Initialize('/js/KupeConstructorData.json', coupeInfo.id);
		}
		catch ( err ) {
			var pageID = $('body').data('id'),
				dataToLog = {
					event: 'Kupe3dConstructor error',
					type:'ошибка загрузки Kupe3dConstructor',
					pageID: pageID,
					err: err
				};
			// end of vars
			
			logError(dataToLog);
		}

		$('.jsBuyButton').off();
		$('.jsBuyButton').bind('click', kupe2basket);
	};


	$(document).ready(function() {
		if ( $('#planner3D').length ) {
			$LAB.script( 'KupeConstructorScript.min.js' ).script( 'three.min.js' ).wait(initPlanner);
		}
	});
}(this));