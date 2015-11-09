$(function(){
   $('.js-private-sections-button').on('click', function(e){
       e.preventDefault();

       var $this = $(this),
           container = $this.closest('.js-private-sections-container'),
           containerB = container.find('.js-private-sections-body'),
           icon = $this.find('.js-private-sections-icon');

       containerB.slideToggle();
       icon.toggleClass('private-sections__button-icon_hide');

   })
});