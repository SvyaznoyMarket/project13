(function(){
  $(function(){
    if($('.bCtg__eMore').length) {
      var expanded = false;
      $('.bCtg__eMore').click(function(){
        if(expanded) {
          $(this).siblings('.more_item').hide();
          $(this).find('a').html('еще...');
        } else {
          $(this).siblings('.more_item').show();
          $(this).find('a').html('скрыть');
        }
        expanded = !expanded;
        return false;
      });
    }

    /* Cards Carousel  */
    function cardsCarouselTag ( nodes, noajax ) {
      var current = 1;

      var wi  = nodes.width*1;
      var viswi = nodes.viswidth*1;

      if( !isNaN($(nodes.times).html()) )
        var max = $(nodes.times).html() * 1;
      else
        var max = Math.ceil(wi / viswi);

      if((noajax !== undefined) && (noajax === true)) {
        var buffer = 100;
      } else {
        var buffer = 2;
      }

      var ajaxflag = false;


      var notify = function() {
        $(nodes.crnt).html( current );
        if(refresh_max_page) {
          $(nodes.times).html( max );
        }
        if ( current == 1 )
          $(nodes.prev).addClass('disabled');
        else
          $(nodes.prev).removeClass('disabled');
        if ( current == max )
          $(nodes.next).addClass('disabled');
        else
          $(nodes.next).removeClass('disabled');
      }

      var shiftme = function() {  
        var boxes = $(nodes.wrap).find('.goodsbox')
        $(boxes).hide()
        var le = boxes.length
        for(var j = (current - 1) * viswi ; j < current  * viswi ; j++) {
          boxes.eq( j ).show()
        }
      }

      $(nodes.next).bind('click', function() {
        if( current < max && !ajaxflag ) {
          if( current + 1 == max ) { //the last pull is loaded , so special shift

            var boxes = $(nodes.wrap).find('.goodsbox')
            $(boxes).hide()
            var le = boxes.length
            var rest = ( wi % viswi ) ?  wi % viswi  : viswi
            for(var j = 1; j <= rest; j++)
              boxes.eq( le - j ).show()
            current++
          } else {
            if( current + 1 >= buffer ) { // we have to get new pull from server

              $(nodes.next).css('opacity','0.4') // addClass dont work ((
              ajaxflag = true
              var getData = []
              if( $('form.product_filter-block').length )
                getData = $('form.product_filter-block').serializeArray()
              getData.push( {name: 'page', value: buffer+1 } )  
              $.get( $(nodes.prev).attr('data-url') , getData, function(data) {
                buffer++
                $(nodes.next).css('opacity','1')
                ajaxflag = false
                var tr = $('<div>')
                $(tr).html( data )
                $(tr).find('.goodsbox').css('display','none')
                $(nodes.wrap).html( $(nodes.wrap).html() + tr.html() )
                tr = null
              })
              current++
              shiftme()
            } else { // we have new portion as already loaded one     
              current++
              shiftme() // TODO repair
            }
          }
          notify()
        }
        return false
      })

      $(nodes.prev).click( function() {
        if( current > 1 ) {
          current--
          shiftme()
          notify()
        }
        return false
      })

      var refresh_max_page = false
    } // cardsCarousel object

    $('.carouseltitle').each( function(){
      if($(this).find('.jshm').html()) {
        var width = $(this).find('.jshm').html().replace(/\D/g,'');
      } else {
        var width = 3;
      }
      cardsCarouselTag({
        'prev'  : $(this).find('.back'),
        'next'  : $(this).find('.forvard'),
        'crnt'  : $(this).find('.none'),
        'times' : $(this).find('span:eq(1)'),
        'width' : width,
        'wrap'  : $(this).find('~ .carousel').first(),
        'viswidth' : 3
      });
    })
  });
})();
