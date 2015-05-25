/**
 * Страницы поставщика (загрузка прайсов) : /supplier/...
 */
+function(){

    var $fileForm = $('#priceForm'),
        $detailsForm = $('#detailsForm'),
        $fileInput = $('#priceInput'),
        $fileButton  = $('#priceButton');

    $fileForm.fileUpload();

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
            success: function(data){
                console.log(data);
            },
            error: function(){
                console.error('User update failed')
            }
        })
    })

}();