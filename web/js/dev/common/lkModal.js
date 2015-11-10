$(function(){
    var self;

   $('.js-modalShow').on('click change', function(e){
      var $this = $(this),
          modal = $('.js-modalLk');
       if(e.type = 'change'){
           if($this.prop("checked")){
               modal.css('display', 'block');
               self = $this;
           }
       }
       if(e.type = 'click'){
           e.preventDefault();

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