/**
 * fileUpload
 * http://abandon.ie
 *
 * Plugin to add file uploads to jQuery ajax form submit
 *
 * November 2013
 *
 * @version 0.9
 * @author Abban Dunne http://abandon.ie
 * @license MIT
 *
 */
;(function($, window, document, undefined)
{
    // Create the defaults once
    var pluginName = "fileUpload",
        defaults = {
            uploadData    : {},
            submitData    : {},
            uploadOptions : {},
            submitOptions : {},
            before        : function(){},
            beforeSubmit  : function(){ return true; },
            success       : function(){},
            error         : function(){},
            complete      : function(){}
        };

    // The actual plugin constructor
    function Plugin(element, options)
    {
        this.element    = element;
        this.$form      = $(element);
        this.$uploaders = $('input[type=file]', this.element);
        this.files      = {};
        this.settings   = $.extend({}, defaults, options);
        this._defaults  = defaults;
        this._name      = pluginName;
        this.init();
    }

    Plugin.prototype =
    {
        /**
         * Initialize the plugin
         *
         * @return void
         */
        init: function()
        {
            this.$uploaders.on('change', { context : this }, this.processFiles);
            this.$form.on('submit', { context : this }, this.uploadFiles);
        },



        /**
         * Process files after they are added
         *
         * @param  jQuery event
         * @return void
         */
        processFiles: function(event)
        {
            var self = event.data.context;
            self.files[$(event.target).attr('name')] = event.target.files;
        },



        /**
         * Handles the file uploads
         *
         * @param  jQuery event
         * @return void
         */
        uploadFiles: function(event)
        {
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening

            var self = event.data.context;

            // Run the before callback
            self.settings.before();

            // Declare a form data object
            var data = new FormData();
            data.append('file_upload_incoming', '1');

            // Add the files
            $.each(self.files, function(key, field)
            {
                $.each(field, function(key, value)
                {
                    data.append(key, value);
                });
            });

            // If there is uploadData passed append it
            $.each(self.settings.uploadData, function(key, value)
            {
                data.append(key, value);
            });

            // Perform Ajax call
            $.ajax($.extend({}, {
                url: self.$form.attr('action'),
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                processData: false, // Don't process the files, we're using FormData
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR){ self.processSubmit(event, data); },
                error: function(jqXHR, textStatus, errorThrown){ self.settings.error(jqXHR, textStatus, errorThrown); }
            }, self.settings.uploadOptions));
        },



        /**
         * Submits form data with files
         *
         * @param  jQuery event
         * @param  object
         * @return void
         */
        processSubmit: function(event, uploadData)
        {
            var self = event.data.context;

            // Run the beforeSubmit callback
            if(!self.settings.beforeSubmit(uploadData)) return;

            // Serialize the form data
            var data = self.$form.serializeArray();

            // Loop through the returned array from the server and add it to the new POST
            $.each(uploadData, function(key, value)
            {
                data.push({
                    'name'  : key,
                    'value' : value
                });
            });

            // If there is uploadData passed append it
            $.each(self.settings.submitData, function(key, value)
            {
                data.push({
                    'name'  : key,
                    'value' : value
                });
            });

            $.ajax($.extend({}, {
                url: self.$form.attr('action'),
                type: 'POST',
                data: data,
                cache: false,
                dataType: 'json',
                success: function(data, textStatus, jqXHR){ self.settings.success(data, textStatus, jqXHR); },
                error: function(jqXHR, textStatus, errorThrown){ self.settings.error(jqXHR, textStatus, errorThrown); },
                complete: function(jqXHR, textStatus){ self.settings.complete(jqXHR, textStatus); }
            }, self.settings.submitOptions));
        }
    };

    $.fn[pluginName] = function(options)
    {
        return this.each(function()
        {
            if(!$.data(this, "plugin_" + pluginName))
            {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
    };

})(jQuery, window, document);

/**
 * Страницы поставщика (загрузка прайсов) : /supplier/...
 */
+function($){

    var $fileForm = $('#priceForm'),
        $detailsForm = $('#detailsForm'),
        $fileInput = $('#priceInput'),
        $fileButton  = $('#priceButton'),
        $editButton = $('.jsEditDetails'),
        $fillFormMessage = $('.jsFillFormSpan'),
        inputErrorClass = 'error',
        validate, initMaskedInput,
        $name = $('[name=detail\\[name\\]]'),
        $nameFull = $('[name=detail\\[name_full\\]]'),
        $address = $('[name=detail\\[legal_address\\]]'),
        $realAddress = $('[name=detail\\[real_address\\]]'),
        $inn = $('[name=detail\\[inn\\]]'),
        $kpp = $('[name=detail\\[kpp\\]]'),
        $account = $('[name=detail\\[account\\]]'),
        $corrAccount = $('[name=detail\\[korr_account\\]]'),
        $bik = $('[name=detail\\[bik\\]]'),
        $okpo = $('[name=detail\\[okpo\\]]');

    if (!/supplier\/cabinet/.test(window.location.href)) return;

    validate = function(){

        var result;

        $detailsForm.find('input').removeClass(inputErrorClass);

        $.each([$name, $nameFull, $address, $realAddress, $inn, $kpp, $account, $corrAccount, $bik, $okpo], function(i,$elem){
            if ($elem.val() == '') $elem.addClass(inputErrorClass);
        });

        result = $detailsForm.find('input.'+inputErrorClass).length > 0;

        if (!result) $fillFormMessage.hide();
        else $fillFormMessage.show();

        return result;
    };

    // jquery.maskedinput
    $.mask.placeholder= " ";
    $.each([$inn, $kpp, $account, $corrAccount, $bik, $okpo], function(i,$elem) {
        $elem.mask($elem.data('mask')+'');
    });

    // plugin init
    // https://github.com/mgiacomini/jQuery-Ajax-File-Upload
    $fileForm.fileUpload({
        beforeSubmit  : function(uploadData){
            console.log (uploadData);
            if (uploadData.success) {
                if (uploadData.html) {
                    $('.jsPricesDiv').show();
                    $('.jsPricesList').prepend(uploadData.html);
                }
            }
            return true;
        },
        success: function(data){
            console.log('Uploaded', data);

        }
    });

    // Открытие окна выбора прайс-листа
    $fileButton.on('click',function(e){
        e.preventDefault();
        $fileInput.click();
    });

    // Отправка прайс-листа при изменении fileinput-а
    $fileInput.on('change', function(){
        $fileForm.submit();
    });

    // Обновление данных о пользователе
    $detailsForm.on('submit', function(e){
        e.preventDefault();
        $.ajax($detailsForm.attr('action'), {
            type: 'POST',
            data: $detailsForm.serialize(),
            success: function(data) {
                console.log(data);
                validate();
                if (data.success) $('.jsAddressSaved').fadeIn().delay(1000).fadeOut();
            },
            error: function(){
                console.error('User update failed')
            }
        })
    });

    $editButton.on('click', function(e){
        e.preventDefault();
        $detailsForm.show();
    });

}(jQuery);
+function($){

    var $supplierLoginButton = $('.jsSupplierLoginButton'),
        $authPopup = $('#auth-block'),
        $title = $authPopup.find('.jsAuthFormLoginTitle'),
        authClass = 'supplier-login',
        inputErrorClass = 'error',
        $registerForm = $('#b2bRegisterForm'),
        validate,
        $inputs = $registerForm.find('input'),
        $detailName = $registerForm.find('[name=detail\\[name\\]]'),
        $userName = $registerForm.find('[name=first_name]'),
        $email = $registerForm.find('[name=email]'),
        $phone = $registerForm.find('[name=mobile]'),
        $agreed = $registerForm.find('[name=agree]');

    $.mask.placeholder= "_";
    $phone.mask('8 (999) 999 99 99');


    /* Функция валидации формы */
    validate = function(){

        // Очищаем классы ошибок
        $inputs.removeClass(inputErrorClass);
        $agreed.next().removeClass('red');

        if ($detailName.val() == '') $detailName.addClass(inputErrorClass);
        if ($userName.val() == '') $userName.addClass(inputErrorClass);
        if (!ENTER.utils.validateEmail($email.val())) $email.addClass(inputErrorClass);
        if ($phone.val() == '') $phone.addClass(inputErrorClass);
        if (!$agreed.is(':checked')) $agreed.next().addClass('red');

        return $registerForm.find('input.error').length == 0 && $agreed.is(':checked');
    };

    // Показ модифицированного окна логина
    $supplierLoginButton.on('click', function(){
        $authPopup.addClass(authClass);
        $title.text('Вход в Enter B2B');
        $authPopup.lightbox_me({
            centered: true,
            onClose: function() {
                $authPopup.removeClass(authClass);
                $title.text('Вход в Enter')
            }
        })
    });

    // Обработчик
    $registerForm.on('submit', function(e) {
        e.preventDefault();
        if (!validate()) return;
        $.ajax($registerForm.attr('action'), {
            type: 'POST',
            data: $registerForm.serialize(),
            success: function(data) {
                console.log('success function', data);
                if (data.success) {
                    $supplierLoginButton.click();
                    // Подставим email в попап логина
                    $authPopup.find('[name=signin\\[username\\]]').val($registerForm.find('[name=email]').val());
                    $('<div style="font-weight: bold; margin: 10px 0; color: gray" />').text('Пароль выслан на телефон и email').insertAfter($title)
                }

                if (data.error == 'Некорректный email' || data.error == 'Такой email уже занят') $registerForm.find('[name=email]').addClass(inputErrorClass);
                if (data.error == '"Должен быть мобильный номер телефона"' || data.error == 'Такой номер уже занят') $registerForm.find('[name=mobile]').addClass(inputErrorClass);
            },
            error: function(){
                console.error('User registration error');
            }
        })
    });

}(jQuery);