$(function(){
    var self;

   $('.js-modalShow').on('change', function(){
      var $this = $(this),
          modal = $('.js-modalLk');
        if($this.prop("checked")){
            modal.css('display', 'block');
            self = $this;
        }


   });

    $('.js-modal-close').on('click', function(e){
        e.preventDefault();

        var modal = $('.js-modalLk');

        modal.css('display', '');
        self.removeAttr('checked');
    })

});