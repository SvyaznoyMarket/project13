/**
 * Enterprize
 *
 * @author  Shaposhnik Vitaly
 */
;(function(ENTER) {
	var
		form = $('.jsEnterprizeForm'),
		body = $('body'),
		mobilePhoneField = $('.jsMobile'),
		authBlock = $('#enterprize-auth-block'),

		/**
		 * Конфигурация валидатора для формы ЛК Enterprize
		 * @type {Object}
		 */
		validationConfig = {
			fields: [
				{
					fieldNode: $('.jsName'),
					require: true,
					customErr: 'Не указано имя'
				},
				{
					fieldNode: mobilePhoneField,
					require: true,
					validBy: 'isPhone',
					customErr: 'Не указан мобильный телефон'
				},
				{
					fieldNode: $('.jsEmail'),
					require: true,
					validBy: 'isEmail',
					customErr: 'Не указан email'
				},
				{
					fieldNode: $('.jsAgree'),
					require: true,
					customErr: 'Необходимо согласие'
				},
				{
					fieldNode: $('.jsSubscribe'),
					require: true,
					customErr: 'Необходимо согласие'
				}
			]
		},
		validator = new FormValidator(validationConfig),

		/**
		 * Конфигурация валидатора для формы логина
		 * @type {Object}
		 */
		signinValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsSigninUsername', authBlock),
					require: true,
					customErr: 'Не указан логин'
				},
				{
					fieldNode: $('.jsSigninPassword', authBlock),
					require: true,
					customErr: 'Не указан пароль'
				}
			]
		},
		signinValidator = new FormValidator(signinValidationConfig),

		/**
		 * Конфигурация валидатора для формы регистрации
		 * @type {Object}
		 */
		forgotPwdValidationConfig = {
			fields: [
				{
					fieldNode: $('.jsForgotPwdLogin', authBlock),
					require: true,
					customErr: 'Не указан email или мобильный телефон',
					validateOnChange: true
				}
			]
		},
		forgotValidator = new FormValidator(forgotPwdValidationConfig);
	// end of vars

	var
		/**
		 * Очистка блока сообщений
		 */
		clearMsg = function clearMsg() {
			$('ul.red').length && $('ul.red').html('');
			$('ul.green').length && $('ul.green').html('');
		},

		/**
		 * Показ сообщений
		 *
		 * @param   {String}    msg     Сообщение которое необходимо показать пользователю
		 * @param   {String}    type    Тип сообщения. Ожидаемое значение 'error' или 'notice'
		 */
		showMsg = function showMsg( msg, type ) {
			var
				type = type ? type : 'error',
				msgClass = 'error' === type ? 'red' : ('notice' === type ? 'green' : null),
				msgBlock = $('ul.' + msgClass);
			// end of vars

			if ( !msgClass ) {
				return;
			}

			if ( msgBlock.length ) {
				msgBlock.html('<li>' + msg + '</li>');
			}
			else {
				form.prepend($('<ul class="' + msgClass + '" />').append('<li>' + msg + '</li>'));
			}

			return false;
		},

		/**
		 * Обработчик ошибок формы
		 *
		 * @param   {Object}    formError   Объект с полем содержащим ошибки
		 */
		formErrorHandler = function formErrorHandler( formError ) {
			var
				field = $('[name="user[' + formError.field + ']"]');
			// end of vars

			var
				clearError = function clearError() {
					validator._unmarkFieldError($(this));
				};
			// end of functions

			console.warn('Ошибка в поле');

			validator._markFieldError(field, formError.message);
			field.bind('focus', clearError);

			return false;
		},

		/**
		 * Обработчик ошибок из ответа от сервера
		 *
		 * @param res
		 */
		serverErrorHandler = function serverErrorHandler( res ) {
			var
				formError = null,
				i;
			// end of vars

			console.warn('Обработка ошибок формы');

			for ( i = res.form.error.length - 1; i >= 0; i-- ) {
				formError = res.form.error[i];

				if ( !formError.message ) {
					continue;
				}

				console.warn(formError);

				if ( formError.field === 'global' ) {
					showMsg(formError.message);
				}
				else {
					formErrorHandler(formError);
				}
			}

			return false;
		},

		/**
		 * Обработчик сабмита формы ЛК Enterprize
		 *
		 * @param e
		 */
		formSubmit = function formSubmit( e ) {
			e.preventDefault();

			var
				formData = $(this).serializeArray(),
				action = $(this).attr('action');
			// end of vars

			var
				/**
				 * Обработчик ответа от сервера
				 * @param response
				 */
				responseFromServer = function responseFromServer( response ) {
					if ( response.error ) {
						if ( response.needAuth ) {
							openAuth();
						}

						console.warn('Form has error');
						serverErrorHandler(response);

						return false;
					}

					if ( response.data.link !== undefined ) {
						window.location.href = response.data.link;
					}
					else if ( response.notice.message ) {
						showMsg(response.notice.message, 'notice');
					}

					return false;
				};
			// end of functions

			$.post(action, formData, responseFromServer, 'json');

			// очищаем блок сообщений
			clearMsg();

			return false;
		},

		epHintPopup = function() {
			console.log('hint');

			var
				btnHintPopup = $('.js-ep-btn-hint-popup'),

				hintPopup = $('.js-ep-hint-popup'),
				hintPopupClose = hintPopup.find('.js-ep-hint-popup-close');
			// end of vars

			var
				showHintPopup = function showHintPopup() {
					hintPopup.fadeIn(100);

					return false;
				},

				closeHintPopup = function closeHintPopup() {
					hintPopup.fadeOut(100);

					return false;
				};
			// end of functions

			btnHintPopup.on('click', showHintPopup);

			hintPopupClose.on('click', closeHintPopup);
		},

		/**
		 * Открыть окно авторизации
		 */
		openAuth = function() {
			ENTER.utils.signinValidationConfig = signinValidationConfig;
			ENTER.utils.signinValidator = signinValidator;
			ENTER.utils.forgotPwdValidationConfig = forgotPwdValidationConfig;
			ENTER.utils.forgotValidator = forgotValidator;

			var
				/**
				 * При закрытии попапа убераем ошибки с полей
				 */
				removeErrors = function() {
					var
						validators = ['signin', 'forgot'],
						validator,
						config,
						self,
						i, j;
					// end of vars

					for (j in validators) {
						validator = eval('ENTER.utils.' + validators[j] + 'Validator');
						config = eval('ENTER.utils.' + validators[j] + 'ValidationConfig');

						if ( !config || !config.fields || !validator ) {
							continue;
						}

						for (i in config.fields) {
							self = config.fields[i].fieldNode;
							self && validator._unmarkFieldError(self);
						}
					}
				};
			// end of functions

			authBlock.lightbox_me({
				centered: true,
				autofocus: true,
				onLoad: function() {
					authBlock.find('input:first').focus();
				},
				onClose: removeErrors
			});

			return false;
		};
	// end of functions

	// устанавливаем маску для поля "Ваш мобильный телефон"
	$.mask.definitions['n'] = '[0-9]';
	mobilePhoneField.length && mobilePhoneField.mask('8 (nnn) nnn-nn-nn');

	body.on('submit', '.jsEnterprizeForm', formSubmit);
	body.on('click', '.jsEnterprizeAuthLink', openAuth);

	// Подключение слайдера товаров
	if ( $('.js-slider').length ) {
		$('.js-slider').goodsSlider();
	}

	// показываем описание фишки
	body.on('click', '.js-enterprize-coupon', function() {
		var $self = $(this),
			template = $('#tplEnterprizeForm'),
			templateHint = template.html(),

			activeClass = 'act',

			html,
			dataValue = $self.data('value')
		;

		$('.js-enterprize-coupon-hint').remove();

		html = Mustache.render( templateHint, dataValue );

		// показываем окно с описанием фишки
		if ( $self.hasClass( activeClass ) ) {
			$self.removeClass( activeClass );
			$('.js-enterprize-coupon-hint').remove();

		} else {
			$('.js-enterprize-coupon').removeClass( activeClass );
			$self.addClass( activeClass );
			$self.closest('.js-enterprize-coupon-parent').after( html );
		}

		if (dataValue.slider.url) {
			var $sliderContainer = $('.js-enterprize-slider-container');

			$sliderContainer.empty();
			if (body.data('enterprizeSliderXhr')) { // если до этого была загрузка слайдера - прибиваем
				try {
					body.data('enterprizeSliderXhr').abort();
				} catch (error) {
					console.error(error);
				}
			}

			var xhr = $.get(dataValue.slider.url);

			xhr.done(function(response) {
				if (response.content) {
					$sliderContainer.html(response.content);
				}
			});
			xhr.always(function() {
				body.data('enterprizeSliderXhr', null);
				$('.js-slider').goodsSlider();
			});

			body.data('enterprizeSliderXhr', xhr);
		}
	});

	body.on('focus', '.js-phone-mask', function() {
		var $self = $(this);

		$.mask.definitions['n'] = '[0-9]';
		$self.length && $self.mask('8 (nnn) nnn-nn-nn');
	});

	$(document).ready(function() {
		if ( $('.epHintPopup').length ) {
			epHintPopup();
		}
	});

	// Открываем информационный попап
//	if ( infoBlock.length ) {
//		openInfoBlock();
//	}

}(window.ENTER));
/**
 * Created by vadimkovalenko on 21.07.14.
 */
$.widget("ui.registerAuth", {

    availableStates: ['authRegistration','update','confirm','setEnterprize'],
    wrapper: undefined, // контейнер для смены данных
    state: 'authRegister',
    authRegistrationForm: '',
    options: {
        state: 'authRegistration',
        wrapper: '.jsBodyWrapper',
        /**
         * @param init callback
         */
        beforeInit: function(self, init) {
            init();
            return self;
        },
        afterInit: function(self) {

        },
        beforeComplete: function(self, init) {
            init();
            return self;
        },
        afterComplete: function(self) {

        }
    },

    _create: function() {
        var
            self = this,
            o = this.options
        ;

        this._state.widget = this; // пробрасываем ссылку на виджет
        this.wrapper = this.element.find(o.wrapper);

        // костыль, запоминаем изначальную форму как тело для шага authRegistration
        this.authRegistrationForm = this.wrapper.html();

        this._setState(o.state);
    },

    /**
     * Активация виджета
     * подразумевается принудительный вызов
     */
    init: function(state) {
        var self = this;
        if(state) {
            this._setState(state);
        }
        return this.options.beforeInit(this, function(){
            return self;
        }).options.afterInit(this);
    },

    complete: function() {
        var self = this;
        this.options.beforeComplete(this, function(){
            return self;
        }).options.afterComplete(this);
        return this;
    },

    _setState: function(state) {
        this._state[state]();
        this.state = state;
        return this;
    },

    /**
     * Применение переданного состояния формы
     * @param form
     * @param state
     */
    applyFormState: function(form,state) {
        this.flushFormState(form);
        if(state.error) {
            this.applyFormErrorMessage(form,state.error);
        }
        if(state.message) {
            this.applyFormMessage(form,state.message);
        }

        for (k in state.fields) {
            if(!state.fields[k].error) {
                continue;
            }
            this.applyFieldError(form,k,state.fields[k].error);
        }

        return this;
    },


    /**
     * Применение переданных ошибок формы
     * Случай когда передается только массив ошибок
     * @param form
     * @param state
     */
    applyFormErrors: function(form,errors) {
        // конвертируем формат
        var state = {fields:{}};
        for (k in errors) {
            if(errors[k].field=='global') {
                state.error = errors[k].message;
                continue;
            }

            state.fields[errors[k].field] = {
                error: errors[k].message
            };
        }
        return this.applyFormState(form,state);
    },


    /**
     * Применение ошибки к полю
     * @param form
     * @param fieldName
     * @param error
     * @returns {*}
     */
    applyFieldError: function(form,fieldName,error) {
        var self = this;
        form
            .find('[name*="'+fieldName+'"]')// т.к. именование может быть group[name][fieldName]
                .before(self.getErrorString(error))
                .addClass('mError')
            .end();
        return this;
    },


    /**
     * Чистим форму от ошибок
     * @param form
     * @returns {*}
     */
    flushFormState: function(form) {
        form
            .find('input').removeClass('mError').end()
            .find('.bErrorText').remove().end()
            .find('.error_list').remove().end()
            .find('.message').remove().end()
        ;
        return this;
    },

    /**
     * Добавляем общее сообщение об ошибке к форме
     *
     * @param form
     * @param message
     * @returns {*}
     */
    applyFormErrorMessage: function(form, message) {
        form.prepend('<ul class="error_list"><li>' + message + '</li></ul>');
        return this;
    },


    /**
     * Добавляем общее сообщение к форме
     *
     * @param form
     * @param message
     * @returns {*}
     */
    applyFormMessage: function(form, message) {
        form.prepend('<p class="message" style="border: 1px solid #ffa901; padding: 2px 5px">' + message + '</p>');
        return this;
    },

    applyMessage: function(message) {
        this.wrapper.prepend('<h2>' + message + '</h2>');
        return this;
    },

    /**
     * Получаем строку с ошибкой для присоединения к полю
     * @param message
     * @returns {string}
     */
    getErrorString: function(message){
        return '<div class="bErrorText"><div class="bErrorText__eInner">' + message + '</div>';
    },


    /**
     * Описываем обработку состояния
     * @private
     */
    _state: {
        authRegistration: function() {
            if(this.widget.state!='authRegistration') {
                this.widget.wrapper.html(this.widget.authRegistrationForm);
            }

            var form = this.widget.wrapper.find('.jsEnterprizeForm');
            form.off();
            form.on('submit', $.proxy(this.widget._formHandler.registration, form));
            $.mask.definitions['n'] = '[0-9]';
            form.find('.jsMobile').mask('8nnnnnnnnnn');

            var form = this.widget.wrapper.find('.jsLoginForm');
            form.off();
            form.on('submit', $.proxy(this.widget._formHandler.login, form));
        },

        update: function() {
            var self = this;
            var init = function(self) {
                var form = self.widget.wrapper.find('.jsEnterprizeForm');
                form.off();
                form.on('submit', $.proxy(self.widget._formHandler.update, form));
            };

            if(this.widget.state!='update') {
                $.get('/updateRegistration?body=1',
                    function(response) {
                        if(typeof(response.error) !== 'undefined' && response.error==401) {
                            self.widget._setState('authRegistration');
                        } else {
                            self.widget.wrapper.html(response.body);
                            init(self);
                        }
                    }
                );
            } else {
                init(self);
            }
        },

        confirm: function() {
            var self = this;
            var init = function(self){
//                var form = self.widget.wrapper.find('.jsConfirmEmail');
//                form.off();
//                form.on('submit', $.proxy(self.widget._formHandler.confirmEmail, form));
//
                var form = self.widget.wrapper.find('.jsConfirmEmailRepeatCode');
                if(form.length>0) {
                    form.off();
                    form.on('submit', $.proxy(self.widget._formHandler.confirmEmailRepeatCode, form));
                }

                var form = self.widget.wrapper.find('.jsConfirmPhone');
                if(form.length>0) {
                    form.off();
                    form.on('submit', $.proxy(self.widget._formHandler.confirmPhone, form));
                }

                var form = self.widget.wrapper.find('.jsConfirmPhoneRepeatCode');
                if(form.length>0) {
                    form.off();
                    form.on('submit', $.proxy(self.widget._formHandler.confirmPhoneRepeatCode, form));
                }

                var form = self.widget.wrapper.find('.jsCheckConfirmForm');
                if(form.length>0) {
                    form.off();
                    form.on('submit', $.proxy(self.widget._formHandler.confirmCheckState, form));
                }
            };

            if(this.widget.state!='confirm') {
                $.ajax({
                    url: '/enterprize/confirm-wc/form',
                    success: function(response,status,state) {
                        if(typeof(response.error) !== 'undefined' && response.error.code==401) {
                            self.widget._setState('authRegistration');
                        } else {
                            self.widget.wrapper.html(response.result.form);
                            init(self);
                        }
                    }
                });
            } else {
                init(self);
            }
        },

        setEnterprize: function() {
            var self = this;
            $.ajax({
                url: '/enterprize/confirm-wc/setEnterprize',
                success: function(response,status,state) {
                    if(response.error && response.error.code==401) {
                        self.widget._setState('authRegistration');
                    } else if (response.error) {
                        self.widget.wrapper.html('');
                        self.widget.applyMessage(response.error.message);
                    } else {
                        self.widget.wrapper.html('');
                        self.widget.applyMessage(response.result.message);
                        window.setTimeout(function(){
                            self.widget.complete();
                        },5000)
                    }
                }
            });
        }
    },

    _formHandler: {
        login: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(response.error) { // если ошибка
                        widget.applyFormErrors(self, response.form.error);
                    } else if(response.data.user.is_enterprize_member) {
                        widget.complete();
                    } else if(!response.data.user.mobile_phone || !response.data.user.email) {
                        widget._setState('update');
                    } else if(!response.data.user.is_email_confirmed || !response.data.user.is_phone_confirmed) {
                        widget._setState('confirm');
                    } else if(!response.data.user.is_enterprize_member) {
                        widget._setState('setEnterprize');
                    } else if (response.alreadyLogged) { // если мы уже залогинены, можем только на корректировку данных кинуть
                        widget._setState('update');
                    } else { // если вообще не понятно чего
                        widget.applyFormState(self,{
                            message: response.message
                        });
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается авторизоваться.'
                    });
                }
            });
            return false;
        },

        registration: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(!response.success) { // если ошибка
                        widget.applyFormState(self,response.form);
                    } else if (response.alreadyLogged) { // если мы уже залогинены
                        widget._setState('update');
                    } else { // если все прошло хорошо, переходим к подтверждению
                        widget._setState('confirm');
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается выполнить регистрацию.'
                    });
                }
            });
            return false;
        },

        update: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if (response.success) {
                        // проверяем состояние подтвержденности контактов
                        $.ajax({
                            type: 'POST',
                            url: '/enterprize/confirm-wc/state',
                            success: function(response){
                                // Если у нас все было подтверждено
                                if(response.status.isEmailConfirmed && response.status.isPhoneConfirmed) {
                                    widget._setState('setEnterprize');
                                } else {
                                    widget._setState('confirm');
                                }
                            }
                        });
                    } else { // если ошибка
                        widget.applyFormState(self, response.form);
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается обновить данные.'
                    });
                }
            });
        },

        confirmPhone: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if (response.error) {
                        widget.applyFormState(self, {
                            error: response.error.message
                        });
                    // проверяем состояние подтверждения
                    // если и email подтвержден, то идем дальше
                    } else if (response.status && response.status.isEmailConfirmed) {
                        widget._setState('setEnterprize');
                    } else {
                        widget.flushFormState(self);
                        widget.applyFormMessage(self,response.message);
                        widget.wrapper.find('.jsPhoneConfirm').hide('slow');
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается подтвердить телефон.'
                    });
                }
            });
            return false;
        },

        confirmEmail: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(response.error) {
                        widget.applyFormState(self,{
                            error: response.error.message
                        });
                    // проверяем состояние подтверждения
                    // если и телефон подтвержден, то идем дальше
                    } else if(response.status.isPhoneConfirmed) {
                        widget._setState('setEnterprize');
                    } else {
                        widget.flushFormState(self);
                        widget.applyFormMessage(self,response.message);
                        widget.wrapper.find('.jsPhoneConfirm').hide('slow');
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается подтвердить email.'
                    });
                }
            });
            return false;
        },

        confirmPhoneRepeatCode: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(response.error) {
                        widget.applyFormState(self,{
                            error: response.error.message
                        });
                    } else {
                        widget.applyFormState(self,{
                            message: response.message
                        });
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается запросить код повторно.'
                    });
                }
            });
            return false;
        },

        confirmEmailRepeatCode: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(response.error) {
                        widget.applyFormState(self,{
                            error: response.error.message
                        });
                    } else {
                        widget.applyFormState(self,{
                            message: response.message
                        });
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается запросить письмо для подтверждения повторно.'
                    });
                }
            });
            return false;
        },

        confirmCheckState: function(e) {
            var self = this;
            var widget = $(this.context).data().uiRegisterAuth;
            $.ajax({
                type: 'POST',
                url: this.attr('action'),
                data: this.serializeArray(),
                success: function(response) {
                    if(response.error) {
                        widget.applyFormState(self,{
                            error: response.error.message
                        });
                    }else if(response.status.isPhoneConfirmed && response.status.isEmailConfirmed) {
                        widget._setState('setEnterprize');
                    } else {
                        var message = "";
                        if(!response.status.isPhoneConfirmed) {
                            message += "Вы еще не подтвердили телефонный номер <br/>";
                        }

                        if(!response.status.isEmailConfirmed) {
                            message += "Вы еще не подтвердили email";
                        }

                        widget.applyFormState(self,{
                            message: message
                        });
                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Извините, не удается подтвердить статус пользователя Enterprize'
                    });
                }
            });
            return false;
        }
    }
});
