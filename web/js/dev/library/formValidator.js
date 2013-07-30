/* 
	Config example

	config = {
		fields: [
			{
				filed: 'jQuery Node',
				validBy: 'String',
				require: 'Boolean',
				customErr: 'String'
			}
		],
		errorClass: 'String'
	}
*/

/**
 * Валидатор форм
 *
 * @author		Zaytsev Alexandr
 * @this		{FormValidator}
 * @requires	jQuery
 * @constructor
 */
function FormValidator( config ) {
	if ( !config.fields.length ) {
		return;
	}

	this.config = $.extend(
						{},
						this.defaultsConfig,
						config );

}

/**
 * Стандартные настройки валидатора
 */
FormValidator.prototype.defaultsConfig = {
	errorClass: 'mError'
};

/**
 * Проверка обязательных к заполнению полей
 */
FormValidator.prototype.requireAs = {
	checkbox : function( filedNode ) {

	},

	radio: function( filedNode ) {

	},

	text: function( filedNode ) {
		var value = filedNode.val();

		if ( value.length === 0 ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			}
		}

		return {
			hasError: false
		};
	},

	textarea: function( filedNode ) {
		var value = filedNode.text();

		if ( value.length === 0 ) {
			return {
				hasError: true,
				errorMsg : 'Поле обязательно для заполнения'
			}
		}

		return {
			hasError: false
		};
	},

	select: function( filedNode ) {
		if ( filedNode.val() ) {
			return {
				hasError: false
			};
		}

		return {
			hasError: true,
			errorMsg : 'Необходимо выбрать значение из списка'
		};
	}
};

FormValidator.prototype.validBy = {
	isEmail: function( filedNode ) {
		var re = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
			value = filedNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Некорректно введен e-mail'
			};
		}
	},

	isPhone: function( filedNode ) {

	},

	isNumber: function( filedNode ) {
		var re = /^[0-9]+$/,
			value = filedNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Поле может содержать только числа'
			};
		}
	}
};

FormValidator.prototype.validateField = function( field ) {
	var self = this,

		elementType = null,

		fieldNode = null,
		validBy = null,
		require = null,
		customErr = '',

		error = {},
		result = {};
	// end of vars

	fieldNode = field.field;
	require = ( fieldNode[0].attr('required') ) ? true : field.require; // если у элемента формы есть required то поле обязательное, иначе брать из конфига
	validBy = field.validBy;
	customErr = field.customErr;

	elementType = ( fieldNode[0].tagName === 'TEXTAREA') : 'textarea' ? ( fieldNode[0].tagName === 'SELECT') : 'select' ? fieldNode.attr('type'); // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type

	// проверка обязательных для заполнения полей
	if ( require ) {
		if ( self.requireAs.hasOwnProperty(elementType) ) {
			result = self.requireAs[elementType](fieldNode);

			if ( result.hasError ) {
				return result;
			}
		}
		else {
			error = {
				hasError: true,
				errorMsg : 'Обязательное поле. Неизвестный метод проверки для '+elementType
			};

			return error;
		}
	}

	// если нет указанного способа валидации
	if ( !validBy ) {
		return {
			hasError: false;
		}
	}

	// валидация выбранным методом
	if ( self.validBy.hasOwnProperty(validBy) ) {
		result = self.validBy[elementType](fieldNode);

		if ( result.hasError ) {
			error = {
				hasError: true,
				errorMsg: ( customErr !== '' ) ? customErr : result.errorMsg
			}

			return error;
		}
	}
	else {
		error = {
			hasError: true,
			errorMsg : 'Неизвестный метод валидации '+elementType
		};

		return error;
	}

};

FormValidator.prototype.validate = function( callbacks ) {
	var self = this,
		fileds = this.config.fields,
		i = 0,
		errors = [],
		result = {};
	// end of vars	

	for ( i = fileds.length - 1; i >= 0; i-- ) { // перебираем поля из конфига
		result = self.validateField(fileds[i]);

		if ( result.hasError ) {
			fileds[i].field.addClass(self.config.errorClass);
			errors.push({
				field: fileds[i].field,
				errorMsg: result.errorMsg
			});
		}
	}
};