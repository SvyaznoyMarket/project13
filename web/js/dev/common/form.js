;(function($){

    var
        $body = $('body'),
        eventName = 'form.result',
        formSelector = '.js-form',

        /**
         * Обрабатывает формы после загрузки документа
         */
        onLoad = function() {
            $(formSelector).each(function(i, el) {
                var
                    $form = $(el),
                    result = $form.data('result')
                    ;

                if (result && ('object' == typeof result)) {
                    $form.trigger(eventName, [result]);
                }
            });
        },

        /**
         * Обрабатывает формы при получении результата
         */
        onResult = function($form, result) {
            var
                $field
                ;

            console.info('typeof result.errors', typeof result.errors);
            if ('object' == typeof result.errors) {
                $.each(result.errors, function(i, error) {
                    if (!error.field) return true;

                    if ('function' != typeof error.render) {
                        error.render = showFieldError
                    }

                    $field = $form.find('[name="' + error.field + '"]');
                    error.render(error, $field, $form)
                })
            }
        },

        /**
         * Отображает ошибку у поля формы
         */
        showFieldError = function(error, $field, $form) {
            $field.addClass('error'); // TODO error.message
        },

        /**
         * Добавляет обработчик
         */
        attachEvent = function() {
            $body.on(eventName, formSelector, function(event, result) {
                var
                    $form = $(this)
                    ;

                console.info('event/form.result', {'event': event, '$form': $form, 'result': result});

                onResult($form, result);
            });
        }
        ;


    attachEvent();
    onLoad();


}(jQuery));