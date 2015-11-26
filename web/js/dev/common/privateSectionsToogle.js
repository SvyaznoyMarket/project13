$(function(){
    function contentShow(e){

            e.preventDefault();

            var $this = $(this),
                container = $this.closest('.js-private-sections-container'),
                containerB = container.find('.js-private-sections-body'),
                icon = $this.find('.js-private-sections-icon') || 0,
                mapTxt = $this.closest('.js-mapShow') || 0;

            containerB.slideToggle();
        if(icon){
            icon.toggleClass('private-sections__button-icon_hide');
        }

        if(mapTxt.hasClass('js-mapShow')){
            mapTxt.toggleClass('active');

            if(mapTxt.hasClass('active')){
                mapTxt.text('Скрыть карту');
            }else{
                mapTxt.text('Показать карту');
            }
        }
    }

    $('.js-private-sections-button').on('click', contentShow);

});