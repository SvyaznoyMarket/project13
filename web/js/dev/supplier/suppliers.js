/**
 * Страницы поставщика (загрузка прайсов) : /supplier/...
 */
+function($){

    var $fileForm = $('#priceForm'),
        $detailsForm = $('#detailsForm'),
        $fileInput = $('#priceInput'),
        $fileButton  = $('#priceButton'),
        inputErrorClass = 'error',
        validate;

    validate = function(){
        var $address = $('[name=detail\\[legal_address\\]]'),
            $realAddress = $('[name=detail\\[real_address\\]]'),
            $inn = $('[name=detail\\[inn\\]]'),
            $kpp = $('[name=detail\\[kpp\\]]'),
            $account = $('[name=detail\\[account\\]]'),
            $corrAccount = $('[name=detail\\[korr_account\\]]'),
            $bik = $('[name=detail\\[bik\\]]'),
            $okpo = $('[name=detail\\[okpo\\]]');

        $detailsForm.find('input').removeClass(inputErrorClass);

        $.each([$address, $realAddress, $inn, $kpp, $account, $corrAccount, $bik, $okpo], function(i,$elem){
            if ($elem.val() == '') $elem.addClass(inputErrorClass);
        });

        return $detailsForm.find('input.'+inputErrorClass).length > 0
    };

    // plugin init
    // https://github.com/mgiacomini/jQuery-Ajax-File-Upload
    $fileForm.fileUpload({
        beforeSubmit  : function(uploadData){
            console.log (uploadData);
            if (uploadData.success) {
                if (uploadData.html) $('.jsPricesList').prepend(uploadData.html);
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

    validate();

}(jQuery);