//var debug = true
//
//
//window.isInFrame = function() {
//  return window.location != window.parent.location
//}
//
//
//// jQuery.extend
//jQuery.extend({
//  log: function(msg) {
//    this.counter = undefined == this.counter ? 1 : (this.counter + 1)
//
//    if (false == debug) return false
//
//    if (window.console) {
//      // Firefox & Google Chrome
//      console.log(msg);
//    }
//    else {
//    // Other browsers
//      var el = $('<div style="position: absolute; top: ' + (this.counter * 18) + 'px; padding: 2px; color: #00ff00; font: normal 12px Courier New; background: #000; opacity: 0.8; filter:progid:DXImageTransform.Microsoft.Alpha(opacity=80); -khtml-opacity: 0.8">#' + this.counter + ': ' + msg + ' <a href="#" style="color: #ff0000; font: bold 14px Arial; text-decoration: none;">&times;</a></div>')
//      el.oneTime(10000, function() {$(this).remove()})
//      el.find('a').bind('click', function() {$(this).parent().remove()})
//
//      $('body').append(el);
//    }
//
//    return true;
//  }
//})
//
//
//// Обработчик событий
//EventHandler = {
//  'trigger': function(e, param) {
//    if ('string' == typeof(param)) {
//      var name = param
//    }
//    else {
//      var el = $(e.target)
//      var name = el.data('event')
//
//      el.trigger(name+'.prepare', param)
//    }
//
//    if ('function' != typeof(callback)) {
//      callback = $.noop
//    }
//
//    $.log('Event ' + name + ' fired')
//    if (typeof this[name] == 'function')
//    {
//      this[name](e, param)
//    }
//  },
//
//  // Ошибка 401
//  'secure': function(e) {
//     e.preventDefault()
//
//    $('#auth-block').lightbox_me({
//        centered: true,
//        onLoad: function() {
//            $('#auth-block').find('input:first').focus()
//        }
//    })
//  },
//
//  // Обновление DOM-элемента
//  'content.update': function(e, param) {
//    e.preventDefault()
//
//    var el = $(e.target)
//
//    el.trigger('content.update.prepare')
//
//    var url = el.is('a') ? el.attr('href') : false
//    if (url) {
//      $.get(url, function(result, status, x) {
//        var target = null == el.data('target') ? el : $(el.data('target'))
//        if (target) {
//          if ('append' == el.data('update')) {
//            target.append(result.data.content)
//          }
//          else {
//            target.replaceWith(result.data.content)
//          }
//
//          el.trigger('content.update.success', [result])
//        }
//      }, 'json')
//    }
//  },
//
//  // Открытие модального окна
//  'window.open': function(e, param) {
//    e.preventDefault()
//
//    var el = $(e.target)
//
//    var href = el.attr('href') + (-1 != el.attr('href').indexOf('?') ? '&' : '?') + 'frame=true'
//
//    /*
//    $.colorbox({
//      href: href,
//      iframe: true,
//      scrolling: false,
//      transition: 'elastic',
//      speed: 250,
//      initialWidth: 1,
//      initialHeight: 1,
//      onClosed: function() {
//        if (el.data('reload')) {
//          window.location.reload()
//        }
//      }
//    })
//    */
//  },
//
//  // Отправка формы
//  'form.submit': function(e, param) {
//    var el = $(e.target)
//
//    if (window.isInFrame()) {
//      el.attr('action', el.attr('action') + (-1 != el.attr('action').indexOf('?') ? '&' : '?') + 'frame=true&reload-parent='+el.data('reload'))
//    }
//  },
//
//  // Ajax-отправка формы
//  'form.ajax-submit': function(e, param) {
//    e.preventDefault()
//
//    var el = $(e.target)
//
//    el.ajaxSubmit({
//      success: function(result) {
//        var target = el.data('target') ? $(el.data('target')) : el
//
//        target.replaceWith(result.data.content);
//
//        el.trigger('form.ajax-submit.success', [result])
//      }
//    })
//  }
//}
//
//
//
//$(document).ready(function() {
//
//  // Настройки colorbox
//  /*
//  $.extend($.colorbox.settings, {
//    opacity: 0.7,
//    fixed: false,
//    close: 'закрыть <span style="font: bold 16px Verdana">&times;</span>'
//  })
//  */
//
//  // Обработчики ajax-ошибок
//  $(document).ajaxError(function(e, x, settings, exception) {
//    /*
//    $.extend($.colorbox.settings, {
//      width: 400,
//      height: 200
//    })
//    */
//
//    if (x.status == 0) {
//      $.colorbox({html: '<h1>Ошибка</h1><br />Не удается подключиться к серверу', fixed: true})
//    } else if (x.status == 401) {
//      EventHandler.trigger(e, 'secure')
//    } else if (x.status == 404) {
//      $.colorbox({html: '<h1>Ошибка 404</h1><br />Запрашиваемая страница не найдена', fixed: true})
//    } else if (x.status == 403) {
//      $.colorbox({html: '<h1>Ошибка 403</h1><br />Время сессии пользователя истекло. Авторизуйтесь заново, пожалуйста', fixed: true})
//    } else if (x.status == 500) {
//      $.colorbox({html: '<h1>Ошибка 500</h1><br />Ошибка сервера', fixed: true})
//    } else if (e == 'parsererror') {
//      $.colorbox({html: '<h1>Ошибка</h1><br />Не удалось обработать ответ сервера', fixed: true})
//    } else if (e == 'timeout') {
//      $.colorbox({html: '<h1>Ошибка</h1><br />Время ожидания ответа истекло', fixed: true})
//    } else {
//      $.colorbox({html: '<h1>Ошибка</h1><br />Неизвестная ошибка.' + "\n" + x.responseText, fixed: true})
//    }
//  })
//
//  // Если окно находится во фрейме, то изменить размер модального окна
//  if (window.isInFrame()) {
//    /*
//    window.parent.$.fn.colorbox.resize({innerHeight: $(document).height(), innerWidth: $(document).width()})
//    */
//  }
//
//  // Подключение стандартных обработчиков
//  $.each({
//    '.event-click': 'click',
//    '.event-submit': 'submit'
//  }, function(selector, eventName) {
//    $(selector).live(eventName, function(e) {
//      EventHandler.trigger(e)
//    })
//  })
//
//})
// JavaScript Document

$(document).ready(function(){

    $('.form input[type=checkbox],.form input[type=radio]').prettyCheckboxes();

        $(".bigfilter dt").click(function(){
        $(this).next(".bigfilter dd").slideToggle(200);
        $(this).toggleClass("current");
        return false;
    });

        $(".f1list dt B").click(function(){
        $(this).parent("dt").next(".f1list dd").slideToggle(200);
        $(this).toggleClass("current");
        return false;
    });

        $(".tagslist dt").click(function(){
        $(this).next(".tagslist dd").slideToggle(200);
        $(this).toggleClass("current");
        return false;
    });

});


/* —Î‡È‰Â ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
        if ($( "#slider-range1" ).length) $( "#slider-range1" ).slider({
            range: true,
            min: 2000,
            max: 200000,
            values: [ 20000, 100000 ],
            slide: function( event, ui ) {
                $( "#amount1" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
            }
        });
        $( "#amount1" ).val( $( "#slider-range" ).slider( "values", 0 ) +
            " - " + $( "#slider-range" ).slider( "values", 1 ) );


        $( "#slider-range2" ).slider({
            range: true,
            min: 4,
            max: 8,
            values: [ 4, 6 ],
            slide: function( event, ui ) {
                $( "#amount2" ).val( ui.values[ 0 ] + " - " + ui.values[ 1 ] );
            }
        });
        $( "#amount2" ).val( $( "#slider-range2" ).slider( "values", 0 ) +
            " - " + $( "#slider-range2" ).slider( "values", 1 ) );


        $( "#slider-range3" ).slider({
            range: true,
            min: 0,
            max: 16000,
            values: [ 200, 10000 ],
            slide: function( event, ui ) {
                $( "#amount3" ).val("1/" + ui.values[ 0 ] + " - 1/" + ui.values[ 1 ] );
            }
        });
        $( "#amount3" ).val( $( "#slider-range3" ).slider( "values", 0 ) +
            " - " + $( "#slider-range3" ).slider( "values", 1 ) );
    });


/* –ÂÈÚËÌ„ ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
    jQuery(this).find('.ratingbox A').hover(function(){
        $("#ratingresult").html(this.innerHTML);
        return false;
    });
});

/* œÓ‚Â‰ÂÌËÂ ÍÌÓÔÓÍ ÔË Ì‡Ê‡ÚËË  ---------------------------------------------------------------------------------------*/
$(document).ready(function(){
    $(".yellowbutton").mousedown(function()   {
    jQuery(this).toggleClass("yellowbuttonactive");
    }).mouseup(function()   {
    jQuery(this).removeClass("yellowbuttonactive");
    });

    $(".whitebutton").mousedown(function()   {
    jQuery(this).toggleClass("whitebuttonactive");
    }).mouseup(function()   {
    jQuery(this).removeClass("whitebuttonactive");
    });

    $(".whitelink").mousedown(function()   {
    jQuery(this).toggleClass("whitelinkactive");
    }).mouseup(function()   {
    jQuery(this).removeClass("whitelinkactive");
    });

    $(".goodsbar .link1").bind( 'click.css', function()   {
        $(this).addClass("link1active")
    })

    $(".goodsbar .link2").bind( 'click.css', function()   {
        //$(this).addClass("link2active")
    })

    $(".goodsbar .link3").bind( 'click.css', function()   {
        //$(this).addClass("link3active");
    })
});
