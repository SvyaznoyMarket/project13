!function( root, module ) {
    'use strict';

    var
        FormValidator = module();
    // end of vars

    // AMD
    if ( typeof define === 'function' && define.amd ) {
        define('FormValidator', [], function() {
            return FormValidator;
        });

    // Node.js or CommonJS
    } else if ( typeof exports !== 'undefined' ) {
        exports.FormValidator = FormValidator;

    // YM Modules
    } else if ( typeof modules !== 'undefined' && typeof modules.define === 'function' ) {
        modules.define('FormValidator', [], function( provide ) {
            provide(FormValidator);
        });

    // Browser global
    } else {
        root.FormValidator = FormValidator;
    }

}( this, function() {
    'use strict';

    var
        /**
         * @classdesc   Валидатор форм
         * @memberOf    module:FormValidator~
         * @constructs  FormValidator
         */
        FormValidator = function FormValidator( config ) {
            if ( !config.fields.length ) {
                return;
            }

            this.config = $.extend(
                                {},
                                this._defaultsConfig,
                                config );

            this._enableHandlers();
        };
    // end of vars

    /**
     * ============ PRIVATE METHODS ===================
     */

    /**
     * Стандартные настройки валидатора
     *
     * @member      _defaultsConfig
     * @memberOf    module:FormValidator~FormValidator
     */
    FormValidator.prototype._defaultsConfig = {
        errorClass: 'error',
        validClass: 'valid'
    };

    /**
     * Проверка обязательных к заполнению полей
     *
     * @member      _requireAs
     * @memberOf    module:FormValidator~FormValidator
     */
    FormValidator.prototype._requireAs = {
        checkbox: function( fieldNode ) {
            var
                value = fieldNode.prop('checked');
            // end of vars

            if ( !value ) {
                return {
                    hasError: true,
                    errorMsg: 'Поле обязательно для заполнения'
                };
            }

            return {
                hasError: false
            };
        },

        radio: function( fieldNode ) {
            var
                checked = fieldNode.filter(':checked').val();
            // end of vars

            if ( checked === undefined ) {
                return {
                    hasError: true,
                    errorMsg: 'Необходимо выбрать пункт из списка'
                };
            }

            return {
                hasError: false
            };
        },

        text: function( fieldNode ) {
            var
                value = fieldNode.val();
            // end of vars

            if ( value.length === 0 ) {
                return {
                    hasError: true,
                    errorMsg: 'Поле обязательно для заполнения'
                };
            }

            return {
                hasError: false
            };
        },

        search: function( fieldNode ) {
            var
                value = fieldNode.val();
            // end of vars

            if ( value.length === 0 ) {
                return {
                    hasError: true,
                    errorMsg: 'Поле обязательно для заполнения'
                };
            }

            return {
                hasError: false
            };
        },

        password: function( fieldNode ) {
            var
                value = fieldNode.val();
            // end of vars

            if ( value.length === 0 ) {
                return {
                    hasError: true,
                    errorMsg: 'Поле обязательно для заполнения'
                };
            }

            return {
                hasError: false
            };
        },

        textarea: function( fieldNode ) {
            var
                value = fieldNode.val();
            // end of vars

            if ( value.length === 0 ) {
                return {
                    hasError: true,
                    errorMsg: 'Поле обязательно для заполнения'
                };
            }

            return {
                hasError: false
            };
        },

        select: function( fieldNode ) {
            if ( fieldNode.val() ) {
                return {
                    hasError: false
                };
            }

            return {
                hasError: true,
                errorMsg: 'Необходимо выбрать значение из списка'
            };
        }
    };

    /**
     * Валидирование поля
     *
     * @member      _validBy
     * @memberOf    module:FormValidator~FormValidator
     */
    FormValidator.prototype._validBy = {
        isEmail: function( fieldNode ) {
            var
                re = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
                value = fieldNode.val();
            // end of vars

            if ( re.test(value) ) {
                return {
                    hasError: false
                };
            } else {
                return {
                    hasError: true,
                    errorMsg: 'Некорректно введен e-mail'
                };
            }
        },

        isPhone: function( fieldNode ) {
            var
                re = /(\+7|8)(-|\s)?(\(\d(-|\s)?\d(-|\s)?\d\s?\)|\d(-|\s)?\d(-|\s)?\d\s?)(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d$/i,
                value = fieldNode.val();
            // end of vars

            if ( re.test(value) ) {
                return {
                    hasError: false
                };
            } else {
                return {
                    hasError: true,
                    errorMsg: 'Некорректно введен телефон'
                };
            }
        },

        isNumber: function( fieldNode ) {
            var
                re = /^[0-9]+$/,
                value = fieldNode.val();
            // end of vars

            if ( re.test(value) ) {
                return {
                    hasError: false
                };
            } else {
                return {
                    hasError: true,
                    errorMsg: 'Поле может содержать только числа'
                };
            }
        }
    };

    /**
     * Валидация поля
     *
     * @method      _validateField
     * @memberOf    module:FormValidator~FormValidator#
     *
     * @this        {FormValidator}
     *
     * @param       {Object}    field           Объект поля для валидации
     * @param       {Object}    field.fieldNode Ссылка на jQuery объект поля
     * @param       {String}    field.validBy   Тип валидации поля
     * @param       {Boolean}   field.require   Является ли поле обязательным к заполению
     * @param       {String}    field.errorMsg  Сообщение об ошибке, если поле не прошло валидацию
     *
     * @return      {Object}    error           Объект с ошибкой
     * @return      {Boolean}   error.hasError  Есть ли ошибка
     * @return      {Boolean}   error.errorMsg  Сообщение об ошибке
     */
    FormValidator.prototype._validateField = function( field ) {
        var
            self        = this,
            elementType = null,
            fieldNode   = null,
            validBy     = null,
            require     = null,
            customErr   = '',
            result      = {},

            error       = {
                hasError: false
            };
        // end of vars

        fieldNode = field.fieldNode;
        require   = ( fieldNode.attr('required') === 'required' ) ? true : field.require; // если у элемента формы есть required то поле обязательное, иначе брать из конфига
        validBy   = field.validBy;
        customErr = field.errorMsg;

        if ( !fieldNode.length ) {
            console.warn('нет поля, не валидируем');

            return error;
        }

        //elementType = ( fieldNode.tagName === 'TEXTAREA') ? 'textarea' : ( fieldNode.tagName === 'SELECT') ? 'select' : fieldNode.attr('type') ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type
        elementType = ( fieldNode.prop('tagName') === 'TEXTAREA') ? 'textarea' : ( fieldNode.prop('tagName') === 'SELECT') ? 'select' : ( fieldNode.attr('type') || 'text' ) ; // если тэг элемента TEXTAREA то тип проверки TEXTAREA, если SELECT - то SELECT, иначе берем из атрибута type

        /**
         * Проверка обязательно ли поле для заполенения
         */
        if ( require ) {
            /**
             * Проверка существования метода проверки на обязательность для данного типа поля
             */
            if ( self._requireAs.hasOwnProperty(elementType) ) {
                result = self._requireAs[elementType](fieldNode);

                if ( result.hasError ) {
                    error = {
                        hasError: true,
                        errorMsg: ( customErr !== undefined ) ? customErr : result.errorMsg
                    };

                    return error;
                }
            } else {
                error = {
                    hasError: true,
                    errorMsg: 'Обязательное поле. Неизвестный метод проверки для '+elementType
                };

                return error;
            }
        }

        /**
         * Проверка существования метода валидации
         * Валидация поля, если не пустое
         */
        if ( self._validBy.hasOwnProperty(validBy) && field.fieldNode.val().length !==0 ) {
            result = self._validBy[validBy](fieldNode);

            if ( result.hasError ) {
                error = {
                    hasError: true,
                    errorMsg: ( customErr !== undefined ) ? customErr : result.errorMsg
                };
            }
        } else if ( validBy !== undefined && field.fieldNode.val().length !==0 ) {
            error = {
                hasError: true,
                errorMsg: 'Неизвестный метод валидации '+validBy
            };
        }

        return error;
    };

    /**
     * Снятие маркировок поля
     *
     * @method      _unmarkField
     * @memberOf    module:FormValidator~FormValidator#
     *
     * @param       {Object}    field       Конфигурация поля поля
     */
    FormValidator.prototype._unmarkField = function( field ) {
        var
            fieldNode = field.fieldNode;

        console.info('Снимаем маркировку');

        if ( typeof this.config.unmarkField === 'function' ) {
            this.config.unmarkField(field);
            return;
        }

        fieldNode.removeClass(this.config.errorClass);
        fieldNode.removeClass(this.config.validClass);
        fieldNode.parent().find('.errtx').remove();

        console.log(fieldNode);
        console.log(fieldNode.parent().find('.errtx'));
    };

    /**
     * Маркировка поля ошибкой
     *
     * @method      _markFieldError
     * @memberOf    module:FormValidator~FormValidator#
     *
     * @param       {Object}    field       Конфигурация поля поля
     * @param       {String}    errorMsg    Сообщение об ошибке
     */
    FormValidator.prototype._markFieldError = function( field, errorMsg ) {
        var
            fieldNode = field.fieldNode;

        if ( typeof this.config.callbackError === 'function' ) {
            this.config.callbackError(field, errorMsg);
        } else {
            fieldNode.addClass(this.config.errorClass);

            if ( errorMsg !== '' && errorMsg !== null && errorMsg !== undefined ) {
                fieldNode.before('<div class="errtx">' + errorMsg + '</div>');
            }
        }

        fieldNode.bind('focus', this._unmarkField.bind(this, field));
    };

    /**
     * Маркировка валидного поля
     *
     * @method      _markFieldValid
     * @memberOf    module:FormValidator~FormValidator#
     *
     * @param       {Object}    field       Конфигурация поля поля
     */
    FormValidator.prototype._markFieldValid = function( field ) {
        var
            fieldNode = field.fieldNode;

        this._unmarkField(field);

        if ( typeof this.config.callbackValid === 'function' ) {
            this.config.callbackValid(field);
            return;
        }

        if ( fieldNode.val() && fieldNode.val().length ) {
            fieldNode.addClass(this.config.validClass);
        }

    };

    /**
     * Активация хандлеров для полей
     *
     * @method      _enableHandlers
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     */
    FormValidator.prototype._enableHandlers = function() {
        // console.groupCollapsed('module:FormValidator~FormValidator#_enableHandlers');

        var
            self = this,
            fields = this.config.fields,
            currentField = null,
            i,

            validateOnBlur = function validateOnBlur( field ) {
                var
                    result      = {};

                if ( field ) {
                    result = self._validateField(field);

                    if ( result.hasError ) {
                        self._markFieldError(field, result.errorMsg);
                    } else {
                        self._markFieldValid(field);
                    }
                } else {
                    //console.log('поле не найдено или тип валидации не существует, хандлер нужно убрать');
                    fieldNode.off('blur', validateOnBlur);
                }

                return false;
            },

            blurHandler = function( field ) {
                console.warn('blur handler');
                var
                    timeout_id = null;

                clearTimeout(timeout_id);
                timeout_id = window.setTimeout(validateOnBlur.bind(this, field), 5);
            };


        for ( i = fields.length - 1; i >= 0; i-- ) {
            currentField = fields[i];

            if ( currentField.fieldNode.length === 0 ) {
                continue;
            }


            if ( currentField.validateOnChange ) {
                if ( currentField.fieldNode.prop('FormValidator_validateOnChange') ) {
                    //console.log('уже вешали');
                    continue;
                }

                currentField.fieldNode.prop('FormValidator_validateOnChange', true);
                currentField.fieldNode.on('blur', blurHandler.bind(self, currentField));
            }
        }

        //console.log(self);
        // console.groupEnd();
    };

    /**
     * Поиск поля
     *
     * @method      _findFieldByNode
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}    nodeToFind      Ссылка на jQuery объект поля которое нужно найти
     *
     * @return      {Object}    Object          Объект с параметрами найденой ноды
     * @return      {Boolean}   Object.finded   Было ли поле найдено
     * @return      {Object}    Object.field    Объект поля из конфига
     * @return      {Number}    Object.index    Порядковый номер поля
     */
    FormValidator.prototype._findFieldByNode = function( nodeToFind ) {
        var
            fields = this.config.fields,
            i;

        for ( i = fields.length - 1; i >= 0; i-- ) {
            if ( fields[i].fieldNode.get(0) === nodeToFind.get(0) ) {
                return {
                    finded: true,
                    field: fields[i],
                    index: i
                };
            }
        }

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
     * @method      validate
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}    callbacks               Объект со ссылками на функции обратных вызовов
     * @param       {Function}  callbacks.onInvalid     Функция обратного вызова, если поля не прошли валидацию. В функцию передается массив объектов ошибок.
     * @param       {Function}  callbacks.onValid       Функция обратного вызова, если поля прошли валидацию
     */
    FormValidator.prototype.validate = function( callbacks ) {
        var
            self   = this,
            fields = this.config.fields,
            i      = 0,
            errors = [],
            result = {};

        for ( i = fields.length - 1; i >= 0; i-- ) { // перебираем поля из конфига
            self._unmarkField(fields[i]);
            result = self._validateField(fields[i]);

            console.log(result);

            if ( result.hasError ) {
                self._markFieldError(fields[i], result.errorMsg);
                errors.push({
                    fieldNode: fields[i].fieldNode,
                    errorMsg: result.errorMsg
                });
            } else {
                console.log('нет ошибки в поле ');
                self._markFieldValid(fields[i]);
                console.log(fields[i].fieldNode);
            }
        }

        if ( errors.length ) {
            callbacks.onInvalid(errors);
        } else {
            callbacks.onValid();
        }
    };

    /**
     * Получить тип валидации для поля
     *
     * @method      getValidate
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}            fieldToFind     Ссылка на jQuery объект поля для которого нужно получить параметры валидации
     *
     * @return      {Object|Boolean}                    Возвращает или конфигурацию валидации для поля, или false
     */
    FormValidator.prototype.getValidate = function( fieldToFind ) {
        var
            findedField = this._findFieldByNode(fieldToFind);

        if ( findedField.finded ) {
            return findedField.field;
        }

        return false;
    };

    /**
     * Установить новый тип валидации для поля. Если поле не найдено, создает новое с указанными параметрами.
     *
     * @method      setValidate
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}    fieldNodeToCange                    Ссылка на jQuery объект поля для которого нужно изменить параметры валидации
     * @param       {Object}    paramsToChange                      Новые свойства валидации поля
     * @param       {String}    paramsToChange.validBy              Тип валидации поля
     * @param       {Boolean}   paramsToChange.require              Является ли поле обязательным к заполению
     * @param       {String}    paramsToChange.customErr            Сообщение об ошибке, если поле не прошло валидацию
     * @param       {Boolean}   paramsToChange.validateOnChange     Нужно ли валидировать поле при его изменении
     */
    FormValidator.prototype.setValidate = function( fieldNodeToCange, paramsToChange ) {
        var
            findedField = this._findFieldByNode(fieldNodeToCange),
            addindField = null;

        if ( findedField.finded ) {
            addindField = $.extend(
                            {},
                            findedField.field,
                            paramsToChange );
            this.config.fields.splice(findedField.index, 1);

        } else {
            paramsToChange.fieldNode = fieldNodeToCange;
            addindField = paramsToChange;
        }

        this.addFieldToValidate(addindField);
    };

    /**
     * Удалить поле для валидации
     *
     * @method      removeFieldToValidate
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}    fieldNodeToRemove   Ссылка на jQuery объект поля которое нужно удалить из списка валидации
     *
     * @return      {Boolean}                       Был ли удален объект из массива полей для валидации
     */
    FormValidator.prototype.removeFieldToValidate = function( fieldNodeToRemove ) {
        var
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
     * @method      addFieldToValidate
     * @memberOf    module:FormValidator~FormValidator#
     * @this        {FormValidator}
     *
     * @param       {Object}    field                   Объект поля для валидации
     * @param       {Object}    field.fieldNode         Ссылка на jQuery объект поля
     * @param       {String}    field.validBy           Тип валидации поля
     * @param       {Boolean}   field.require           Является ли поле обязательным к заполению
     * @param       {String}    field.errorMsg          Сообщение об ошибке, если поле не прошло валидацию
     * @param       {Boolean}   field.validateOnChange  Нужно ли валидировать поле при его изменении
     */
    FormValidator.prototype.addFieldToValidate = function( field ) {
        this.config.fields.push(field);
        this._enableHandlers();
    };

    return FormValidator;
});
