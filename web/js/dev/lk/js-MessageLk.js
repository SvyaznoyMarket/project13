$(function(){
    $('.js-MessageAll').on('change click', function(){
        var $this = $(this),
            container = $this.closest('.js-messageContainer'),
            messageCheckbox = container.find('.js-messageCheckbox');

        if($this.prop("checked")){
            messageCheckbox.attr('checked', 'checked');
        }else{
            messageCheckbox.removeAttr('checked');
        }
    });

    $('.js-messageRead').on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            container = $this.closest('.js-messageContainer'),
            messageCheckbox = container.find('.js-messageCheckbox'),
            message = container.find('.js-message');

        if(messageCheckbox.prop("checked")){
            message.removeClass('message-list__item_new-center');
        }

        console.log(messageCheckbox.prop("checked"));
    });

    $('.js-messageRemove').on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            container = $this.closest('.js-messageContainer'),
            messageCheckbox = container.find('.js-messageCheckbox'),
            message = container.find('.js-message');

        if(messageCheckbox.prop("checked")){
            message.remove();
        }
    });
});