$(document).ready(function(){
  if($('body.jewel .filter-section').length) {

    handle_url_hash()

    function handle_small_tabs() {
      $('.brand-subnav__list a').click(function(event){
        $('.brand-subnav__list a').removeClass('active')
        $(this).addClass('active')
        $('#ajaxgoods_top').show()
        get_jewel_content($(this).attr('href'))
        event.stopPropagation()
        return false
      })
    }

    function handle_jewel_filters_pagination() {
      $('.filter-section a, .pageslist a').click(function(event){
        $('#ajaxgoods_top').show()
        get_jewel_content($(this).attr('href'))
        event.stopPropagation()
        return false
      })
    }

    /* Infinity scroll */
    var ableToLoadJewel = true
    function liveScrollJewel( lsURL, filters, pageid ) {
      var params = []
      tmpnode = $('.items-section__list')

      var loader =
        "<div id='ajaxgoods' class='bNavLoader'>" +
          "<div class='bNavLoader__eIco'><img src='/images/ajar.gif'></div>" +
          "<div class='bNavLoader__eM'>" +
            "<p class='bNavLoader__eText'>Подождите немного</p>"+
            "<p class='bNavLoader__eText'>Идет загрузка</p>"+
          "</div>" +
        "</div>"
      tmpnode.after( loader )

      if( lsURL.match(/\?/) )
        lsURL += '&page=' + pageid
      else
        lsURL += '?page=' + pageid

      $.get( lsURL, params, function(data){
        if ( data != "" && !data.data ) { // JSON === error
          ableToLoadJewel = true
          tmpnode.append(data.products)
        }
        $('#ajaxgoods').remove()
        if( $('#dlvrlinks').length ) {
          var coreid = []
          var nodd = $('<div>').html( data.products )
          nodd.find('div.boxhover, div.goodsboxlink').each( function() {
            var cid = $(this).data('cid') || 0
            if( cid )
              coreid.push( cid )
          })
          dajax.post( dlvr_node.data('calclink'), coreid )
        }
        handle_custom_items()
      })
    }

    function handle_jewel_infinity_scroll() {
      $('div.allpagerJewel').each(function() {
        var lsURL = $(this).data('url') 
        var filters = ''//$(this).data('filter')
        var vnext = ( $(this).data('page') !== '') ? $(this).data('page') * 1 + 1 : 2
        var vinit = vnext - 1
        var vlast = parseInt('0' + $(this).data('lastpage') , 10)

        function checkScrollJewel(){
          if ( ableToLoadJewel && $(window).scrollTop() + 800 > $(document).height() - $(window).height() ){
            ableToLoadJewel = false
            if( vlast + vinit > vnext ) {
              liveScrollJewel( lsURL, filters, ((vnext % vlast) ? (vnext % vlast) : vnext ))
            }
            vnext += 1
          }
        }

        $(this).click(function(){
          docCookies.setItem( false, 'infScroll', 1, 4*7*24*60*60, '/' )
          switch_to_scroll(checkScrollJewel)

          $('#ajaxgoods_top').show()
          $.get(lsURL,{},function(data){
            $(window).bind('scroll', function(){
              checkScrollJewel()
            })
          }).done(function(){
            $('#ajaxgoods_top').hide()
          })
        })
      })
      setTimeout(function(){
        if( docCookies.hasItem( 'infScroll' ) ) {
          switch_to_scroll(checkScrollJewel)
        }
      },600)
    }

    function get_jewel_content(url, slide) {
      $.get(url,{},function(data){
        if(slide) {
          $('#smalltabs').slideUp(20)
          $('.filter-section').slideUp(20)
          $('#pagerWrapper').slideUp(150)
        }
        $('#smalltabs').html(data.tabs)
        $('.filter-section').html(data.filters)
        $('#pagerWrapper').html(data.pager)
        if(slide) {
          $('#smalltabs').slideDown(30)
          $('.filter-section').slideDown(30)
          $('#pagerWrapper').slideDown(150)
        }
        handle_small_tabs()
        handle_jewel_filters_pagination()
        handle_custom_items()
        handle_jewel_infinity_scroll()
        if(data.query_string) {
          window.location.hash = data.query_string
        }
      }).done(function(){
        $('#ajaxgoods_top').hide()
      })
    }

    function switch_to_scroll(checkScrollJewel) {
      var next = $('div.pageslist:first li:first')
      if( next.hasClass('current') )
        next = next.next()
      var next_a = next.find('a')
              .html('<span>123</span>')
              .addClass('borderedR')
      next_a.attr('href', next_a.attr('href').replace(/page=\d+/,'') )
      $('div.pageslist li').remove()
      $('div.pageslist ul').append( next )
                 .find('a')
                 .bind('click', function(event){
                    docCookies.removeItem( 'infScroll' )
                    $(window).unbind('scroll')
                    return false
                  })
      handle_jewel_filters_pagination()
      $('div.allpagerJewel').addClass('mChecked')
      checkScrollJewel()
      $(window).scroll(checkScrollJewel)
    }

    function handle_url_hash() {
      if(window.location.hash) {
        join = '?'
        if(matches = window.location.hash.match(/.*(scrollTo=[^&]*)/)) {
          join = join + matches[1] + '&'
        }
        url = window.location.origin + window.location.pathname + join + window.location.hash.replace('#','').replace(/((\?|&)scrollTo=[^&]*)/,'')
      } else {
        url = window.location.origin + window.location.pathname + '?' + window.location.search
      }
      get_jewel_content(url, true)
    }

  }
})