;(function($){

    var
        $body = $('body'),
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
                    $form.trigger('form.result', [result]);
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

                    $field = $form.find('[data-field-container="' + error.field + '"]');
                    error.render(error, $field, $form)
                })
            }
        },

        onReset = function($form) {
            $form.find('[data-field-container]').each(function(i, field) {
                var $field = $(field);

                $field.removeClass('error');
                $field.find('[data-message]').text('');
            });
        },

        /**
         * Отображает ошибку у поля формы
         */
        showFieldError = function(error, $field, $form) {
            $field.addClass('error');
            $field.find('[data-message]').text(error.message);
        },

        /**
         * Добавляет обработчик
         */
        attachEvent = function() {
            $body.on('form.result', formSelector, function(event, result) {
                var
                    $form = $(this)
                    ;

                console.info('event/form.result', {'event': event, '$form': $form, 'result': result});

                onResult($form, result);
            });

            $body.on('form.reset', formSelector, function(event, result) {
                var
                    $form = $(this)
                ;

                console.info('event/form.reset', {'event': event, '$form': $form, 'result': result});

                onReset($form, result);
            });
        }
    ;


    attachEvent();
    onLoad();


}(jQuery));