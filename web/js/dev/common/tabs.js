$(function(){
    $('.js-tabs-control').on('click', function(e){
        e.preventDefault();

        var $this = $(this),
            container = $this.closest('.js-tabs'),
            item = container.find('.js-tab');

        $this.addClass('active')
            .siblings()
            .removeClass('active');

        item.eq($this.filter('.active').index())
            .addClass('active')
            .siblings()
            .removeClass('active');

    })
});