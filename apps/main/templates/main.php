<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_combined_stylesheets() //include_stylesheets() ?>
    <?php //include_javascripts() ?>
    <?php include_component('page', 'link_rel_canonical') ?>
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-25485956-1']);
      _gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
      _gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
      _gaq.push(['_addOrganic', 'nigma.ru', 's']);
      _gaq.push(['_addOrganic', 'webalta.ru', 'q']);
      _gaq.push(['_addOrganic', 'aport.ru', 'r']);
      _gaq.push(['_addOrganic', 'poisk.ru', 'text']);
      _gaq.push(['_addOrganic', 'km.ru', 'sq']);
      _gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
      _gaq.push(['_addOrganic', 'quintura.ru', 'request']);
      _gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
      _gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
      _gaq.push(['_addOrganic', 'gogo.ru', 'q']);
      _gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
      _gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
      _gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
      _gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
      _gaq.push(['_addOrganic', 'ru.search.yahoo.com','p']);
      _gaq.push(['_addOrganic', 'ya.ru', 'q']);
      _gaq.push(['_addOrganic', 'm.yandex.ru','query']);
      _gaq.push(['_trackPageview']);
      _gaq.push(['_trackPageLoadTime']);
      (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();
    </script>
  </head>

  <body>
    <?php LastModifiedHandler::setLastModified(); ?>

    <div class="bannersbox">
      <div class="bannersboxinner">
        <div class="banner banner3"><img class="rightImage" src="" alt="" /></div>
        <div class="banner banner4"><img class="leftImage" src="" alt="" /></div>
      </div>
    </div>

    <?php include_component('banner', 'show', array('view' => 'main')) ?>

    <div class="allpage">

      <div class="logo">Enter Связной</div>
      <!-- Topmenu -->
      <?php include_component('productCategory', 'root_list') ?>
      <!-- /Topmenu -->

      <noindex>
        <div class="searchbox">
          <?php include_component('search', 'form', array('view' => 'main')) ?>
        </div>
      </noindex>

      <div class="bigbanner">
        <div class='bCarouselWrap'>
          <div class='bCarousel'>
            <div class='bCarousel__eBtnL leftArrow'></div>
            <div class='bCarousel__eBtnR rightArrow'></div>
            <img class="centerImage" src="" alt=""/>
          </div>
        </div>
      </div>

      <?php include_component('default', 'footer', array('view' => 'main')) ?>

      <div class="clear"></div>
    </div>

<?php include_combined_javascripts() ?>
<script>
	$(document).ready(function() {

		if( !$('#main_banner-data').length )
        	return
		var promos = $('#main_banner-data').data('value')

		/* Shit happens */
		for(var i=0; i < promos.length; i++ ) {
			if( typeof(promos[i].imgb) === 'undefined' || typeof(promos[i].imgs) === 'undefined') {
				promos.splice( i,1 )
			}
			if( typeof(promos[i].url) === 'undefined' ) {
				promos[i].url = ''
			}
			if( typeof(promos[i].t) === 'undefined' ) {
				promos[i].url = 4000
			}
			if( typeof(promos[i].alt) === 'undefined' ) {
				promos[i].url = ''
			}
		}
		var l = promos.length
		if( l == 0 )
			return
                if( l == 1 ) {
                  $('.centerImage').attr('src', promos[0].imgb ).data('url', promos[0].url)
                  .click( function() {
                    location.href = $(this).data('url')
                  })
                  return
                }
		/* Preload */
		var hb = $('<div>').css('display','none')
		for(var i=0; i < l; i++ ) {
			$('<img>').attr('src', promos[i].imgb).appendTo( hb )
			$('<img>').attr('src', promos[i].imgs).appendTo( hb )
		}
		$('body').append( hb )

        /* Init */
        $('.leftImage').attr({ "src": promos[l - 1].imgs, "alt": promos[l - 1].alt, "title": promos[l - 1].alt})
        $('.centerImage').attr('src', promos[0].imgb ).data('url', promos[0].url)
        $('.rightImage').attr({ "src": promos[1].imgs, "alt": promos[1].alt, "title": promos[1].alt})
        var currentSl = promos.length - 1
        var idto = null
        var initis = []
        var sliding = false
        changeSrc( currentSl )
        idto = setTimeout( function() { goSlide() }, initis[1].t )
        /* Visuals */
        //		$("html").css('overflow-x','hidden')

        var userag    = navigator.userAgent.toLowerCase()
        var isAndroid = userag.indexOf("android") > -1
        var isOSX     = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 )

        if( isAndroid || isOSX ) {
          $('.bCarousel div').show()
          $('.allpage').css('overflow','hidden')
        } else {
          $('.bCarousel').mouseenter( function() {
            $('.bCarousel div').show()
          }).mouseleave( function() {
            $('.bCarousel div').hide()
          })
        }
        $('.leftArrow').click( function() { goSlide( -1 ) } )
        $('.leftImage').click( function() { goSlide( -1 ) } )
        $('.rightArrow').click( function() { goSlide( 1 ) } )
        $('.rightImage').click( function() { goSlide( 1 ) } )
        $('.centerImage').click( function() {
          clearTimeout( idto )
          location.href = $(this).data('url')
        })
        $('.promos').click( function(){ location.href = $(this).data('url') } )

        function sideBanner( block, i ) {
          $(block).animate( {
            "opacity" : "0"
          },
          400,
          function() {
            setTimeout( function() {
              block.attr({ "src": initis[i].imgs, "alt": initis[i].alt, "title": initis[i].alt})
              $(block).animate({
                "opacity" : "1"
              })
            }, 350)
          })
        }

        function changeSrc( currentSl ) {
          var delta = promos.length - currentSl - 3
          if( delta >= 0 )
            initis = promos.slice( currentSl, currentSl + 3 )
          else
            initis = promos.slice( currentSl ).concat( promos.slice(0, -delta) )
          //console.info(currentSl, delta);console.info(initis)
        }

        function goSlide( dir ) {
          if( sliding )
            return false
          sliding = true
          if( !dir )
            var dir = 1
          else // custom summon
            clearTimeout( idto )
          var shift   = '-=1000px',
          inileft = '1032px'

          if( dir < 0 ) {
            shift   = '+=1000px',
            inileft = '-968px'
          }
          currentSl = (currentSl + dir) % promos.length
          if( currentSl < 0 )
            currentSl = promos.length - 1
          changeSrc( currentSl )

          $('.centerImage').animate(
          {
            'left' : shift,
            'opacity' : '0'
          },
          800,
          function() {
            $('.centerImage').attr("src", initis[1].imgb ).data('url', initis[1].url)
            $(this).css('left', inileft).animate({
              'opacity' : '1',
              'left' : shift
            })
            sliding = false
            idto = setTimeout( function() { goSlide() }, initis[1].t ) // AUTOPLAY
          })
          sideBanner($('.leftImage'), 0)
          sideBanner($('.rightImage'), 2)
        }

        /* Search */
        $('.startse').bind ({
          'blur': function() {
            if (this.value == '')
              this.value = 'Поиск среди 20 000 товаров'
          },
          'focus': function() {
            if (this.value == 'Поиск среди 20 000 товаров')
              this.value = ''
          }
        })

      })

    </script>
    <!-- Yandex.Metrika counter -->
    <div style="display:none;"><script type="text/javascript">
      (function(w, c) {
        (w[c] = w[c] || []).push(function() {
          try {
            w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true});
          }
          catch(e) { }
        });
      })(window, "yandex_metrika_callbacks");
      </script></div>
    <script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
    <noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
    <!-- AdHands -->
    <script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
    <script type="text/javascript">
      var report = new adhandsReport ('http://sedu.adhands.ru/site/');
      report.id('1053');
      report.send();
    </script>
    <noscript>
      <img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;">
    </noscript>
    <!-- /AdHands -->

    <?php if (has_slot('seo_counters_advance')): ?>
      <?php include_slot('seo_counters_advance') ?>
    <?php endif ?>


  </body>
</html>
