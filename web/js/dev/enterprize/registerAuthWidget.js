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

    destroy: function(){
        debugger;
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
        this.flushErrors(form);
        if(state.error) {
            this.applyFormErrorMessage(form,state.error);
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
    flushErrors: function(form) {
        debugger;
        form
            .find('input').removeClass('mError').end()
            .find('.bErrorText').remove().end()
            .find('.error_list').remove().end()
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
                var form = self.widget.wrapper.find('.jsConfirmEmail');
                form.off();
                form.on('submit', $.proxy(self.widget._formHandler.confirmEmail, form));

                var form = self.widget.wrapper.find('.jsConfirmEmailRepeatCode');
                form.off();
                form.on('submit', $.proxy(self.widget._formHandler.confirmEmailRepeatCode, form));

                var form = self.widget.wrapper.find('.jsConfirmPhone');
                form.off();
                form.on('submit', $.proxy(self.widget._formHandler.confirmPhone, form));

                var form = self.widget.wrapper.find('.jsConfirmPhoneRepeatCode');
                form.off();
                form.on('submit', $.proxy(self.widget._formHandler.confirmPhoneRepeatCode, form));
            };

            if(this.widget.state!='confirm') {
                $.ajax({
                    url: '/enterprize/confirm-wc/form',
                    success: function(response,status,state) {
                        if(typeof(response.error) !== 'undefined' && response.error.code==401) {
                            self.widget._setState('authRegistration');
                        } else {
                            self.widget.wrapper.html(response);
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
                    if(typeof(response.error) !== 'undefined' && response.error.code==401) {
                        self.widget._setState('authRegistration');
                    } else {
                        debugger;
//                        self.widget.wrapper.html();
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
                    } else if (response.alreadyLogged) { // если мы уже залогинены
                        widget._setState('update');
                    } else {
                        if(response.data.user.is_enterprize_member) {
                            widget.complete();
                        } else if(!response.data.user.mobile_phone || !response.data.user.email) {
                            widget._setState('update');
                        } else {
                            widget._setState('confirm');
                        }
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
                    if(!response.success) { // если ошибка
                        widget.applyFormState(self,response.form);
                    } else { // если все прошло хорошо, переходим к подтверждению
                        widget._setState('confirm');
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
                    if(response.error) {
                        widget.applyFormState(self,{
                            error: response.error.message
                        });
                    } else {
                        widget._setState('setEnterprize');
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
                    } else {
                        widget._setState('setEnterprize');
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

                    }
                },
                error: function(xhr, status, errorThrown) {
                    widget.applyFormState(self,{
                        error: 'Не удается запросить письмо для подтверждения повторно.'
                    });
                }
            });
            return false;
        }
    }
});
