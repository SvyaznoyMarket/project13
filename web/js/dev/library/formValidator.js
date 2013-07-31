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
						this._defaultsConfig,
						config );

}

/**
 * ============ PRIVATE METHODS ===================
 */

/**
 * Стандартные настройки валидатора
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._defaultsConfig = {
	errorClass: 'mError'
};

/**
 * Проверка обязательных к заполнению полей
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._requireAs = {
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

/**
 * Валидирование поля
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._validBy = {
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
		var re = /(\+7|8)(-|\s)?(\(\d(-|\s)?\d(-|\s)?\d\s?\)|\d(-|\s)?\d(-|\s)?\d\s?)(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d$/i;
			value = filedNode.val();

		if ( re.test(value) ) {
			return {
				hasError: false
			};
		}
		else {
			return {
				hasError: true,
				errorMsg : 'Некорректно введен телефон'
			};
		}
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

/**
 * Валидация поля
 * 
 * @param	{Object}	field			Объект поля для валидации
 * @param	{Object}	field.fieldNode	Ссылка на jQuery объект поля
 * @param	{String}	field.validBy	Тип валидации поля
 * @param	{Boolean}	field.require	Является ли поле обязательным к заполению
 * @param	{String}	field.customErr	Сообщение об ошибке, если поле не прошло валидацию
 *
 * @return	{Object}	error			Объект с ошибкой
 * @return	{Boolean}	error.hasError	Есть ли ошибка
 * @return	{Boolean}	error.errorMsg	Сообщение об ошибке
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._validateField = function( field ) {
	var self = this,

		elementType = null,

		fieldNode = null,
		validBy = null,
		require = null,
		customErr = '',

		error = {
			hasError: false
		},
		result = {};
	// end of vars
	console.info('validateField');

	fieldNode = field.filedNode;
	require = ( fieldNode.attr('required') === 'required' ) ? true : field.require; // если у элемента формы есть required то поле обязательное, иначе брать из конфига
	validBy = field.validBy;
	customErr = field.customErr;

	console.log(field);
	console.log('filed require '+field.require);
	console.log('field attr require '+fieldNode.attr('required'))

	elementType = ( fieldNode.tagName === 'TEXTAREA') ? 'textarea' : ( fieldNode.tagName === 'SELECT') ? 'select' : fieldNode.attr('type') ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type

	console.log(elementType)
	console.log(require)

	if ( require ) {
		// поле обязательно для заполнения
		if ( self._requireAs.hasOwnProperty(elementType) ) {
			result = self._requireAs[elementType](fieldNode);

			if ( result.hasError ) {
				error = {
					hasError: true,
					errorMsg : ( customErr !== undefined ) ? customErr : result.errorMsg
				};

				return error;
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

	// валидация выбранным методом
	if ( self._validBy.hasOwnProperty(validBy) ) {
		result = self._validBy[validBy](fieldNode);

		if ( result.hasError ) {
			error = {
				hasError: true,
				errorMsg: ( customErr !== undefined ) ? customErr : result.errorMsg
			}
		}
	}
	else if ( validBy !== undefined ) {
		error = {
			hasError: true,
			errorMsg : 'Неизвестный метод валидации '+validBy
		};
	}

	return error;
};

/**
 * Поиск поля
 * 
 * @param	{Object}	nodeToFind		Ссылка на jQuery объект поля которое нужно найти
 * @return	{Object}    Object			Объект с параметрами найденой ноды
 * @return	{Boolean}	Object.finded	Было ли поле найдено
 * @return	{Boolean}	Object.field	Объект поля из конфига
 * @return	{Boolean}	Object.index	Порядковый номер поля
 *
 * @this	{FormValidator}
 * @private
 */
FormValidator.prototype._findFieldByNode = function( nodeToFind ) {
	var fileds = this.config.fields;

	for ( var i = fileds.length - 1; i >= 0; i-- ) {
		if ( fileds[i].filedNode.selector === nodeToFind.selector ) {
			return {
				finded: true,
				field: fileds[i],
				index: i
			};
		}
	};

	return {
		finded: false
	};
};



/**
 * ============ PUBLIC METHODS ===================
 */


/**
 * Запуск валидации полей
 *
 * @param	{Object}	callbacks			Объект со ссылками на функции обратных вызовов
 * @param	{function}	callbacks.onInvalid	Функция обратного вызова, если поля не прошли валидацию. В функцию передается массив объектов ошибок.
 * @param	{function}	callbacks.onValid	Функция обратного вызова, если поля прошли валидацию
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.validate = function( callbacks ) {
	var self = this,
		fileds = this.config.fields,
		i = 0,
		errors = [],
		result = {};
	// end of vars	
	
	for ( i = fileds.length - 1; i >= 0; i-- ) { // перебираем поля из конфига
		result = self._validateField(fileds[i]);

		if ( result.hasError ) {
			fileds[i].filedNode.addClass(self.config.errorClass);
			errors.push({
				filedNode: fileds[i].filedNode,
				errorMsg: result.errorMsg
			});
		}
	}

	if ( errors.length ) {
		callbacks.onInvalid(errors);
	}
	else {
		callbacks.onValid();
	}
};

/**
 * Установить новый тип валидации для поля
 *
 * @param	{Object}	fieldNodeToRemove	Ссылка на jQuery объект поля для которого нужно изменить параметры валидации
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.setValidate = function( fieldNodeToCange, paramsToChange ) {
	var fileds = this.config.fields,
		findedField = this._findFieldByNode(fieldNodeToCange);

	if ( findedField.finded ) {
		findedField.field = $.extend(
						{},
						findedField.field,
						paramsToChange );
	}
	else {
		paramsToChange.fieldNode = fieldNodeToCange;
		this.addFieldToValidate(paramsToChange);
	}
};

/**
 * Удалить поле для валидации
 * 
 * @param	{Object}	fieldNodeToRemove	Ссылка на jQuery объект поля которое нужно удалить из списка валидации
 *
 * @return	{Boolean}						Был ли удален объект из массива полей для валидации
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.removeFieldToValidate = function( fieldNodeToRemove ) {
	var fileds = this.config.fields,
		findedField = this._findFieldByNode(fieldNodeToRemove);

	if ( findedField.finded ) {
		this.config.fields.splice(findedField.index, 1);

		return true;
	}

	return false;
};

/**
 * Добавить поле для валидации
 * 
 * @param	{Object}	field			Объект поля для валидации
 * @param	{Object}	field.fieldNode	Ссылка на jQuery объект поля
 * @param	{String}	field.validBy	Тип валидации поля
 * @param	{Boolean}	field.require	Является ли поле обязательным к заполению
 * @param	{String}	field.customErr	Сообщение об ошибке, если поле не прошло валидацию
 *
 * @this	{FormValidator}
 * @public
 */
FormValidator.prototype.addFieldToValidate = function( field ) {
	this.config.fileds.push(field);
};