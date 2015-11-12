$(function(){
    var self;

   $('.js-modalShow').on('click change', function(e){
      var $this = $(this),
          modal = $('.js-modalLk');

       if($(e.target).is('input')){
           console.log($(e.target));
           if($this.prop("checked")){
               //console.log($this.prop("checked"));
               modal.css('display', 'block');
               self = $this;
           }
       }else{
           e.preventDefault();

           console.log(e.target);

           console.log(23);
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