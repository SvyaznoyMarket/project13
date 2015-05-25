/**
 * Страницы поставщика (загрузка прайсов) : /supplier/...
 */
+function(){

    var $form = $('#priceForm'),
        $fileInput = $('#priceInput'),
        $button  = $('#priceButton');

    $form.fileUpload();

    $button.on('click',function(e){
        e.preventDefault();
        $fileInput.click();
    });

    $fileInput.on('change', function(){
        console.log('file input');

        $form.submit();
    })

}();