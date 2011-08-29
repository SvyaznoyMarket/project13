var debug = true


window.isInFrame = function() {
  return window.location != window.parent.location
}


// jQuery.extend
jQuery.extend({
  log: function(msg) {
    this.counter = undefined == this.counter ? 1 : (this.counter + 1)

    if (false == debug) return false

    if (window.console) {
      // Firefox & Google Chrome
      console.log(msg);
    }
    else {
    // Other browsers
      var el = $('<div style="position: absolute; top: ' + (this.counter * 18) + 'px; padding: 2px; color: #00ff00; font: normal 12px Courier New; background: #000; opacity: 0.8; filter:progid:DXImageTransform.Microsoft.Alpha(opacity=80); -khtml-opacity: 0.8">#' + this.counter + ': ' + msg + ' <a href="#" style="color: #ff0000; font: bold 14px Arial; text-decoration: none;">&times;</a></div>')
      el.oneTime(10000, function() {$(this).remove()})
      el.find('a').bind('click', function() {$(this).parent().remove()})

      $('body').append(el);
    }

    return true;
  }
})


// Обработчик событий
EventHandler = {
  'trigger': function(e, param) {
    if ('string' == typeof(param)) {
      var name = param
    }
    else {
      var el = $(e.target)
      var name = el.data('event')

      el.trigger(name+'.prepare', param)
    }

    if ('function' != typeof(callback)) {
      callback = $.noop
    }

    $.log('Event ' + name + ' fired')
    if (typeof this[name] == 'function')
    {
      this[name](e, param)
    }
  },

  // Ошибка 401
  'secure': function(e) {
     e.preventDefault()

      $.colorbox({
        iframe: true,
        href: $('#auth-form').find('form:first').attr('action') + '?frame=true',
        scrolling: false,
        fixed: true,
        initialWidth: 1,
        initialHeight: 1
      })
  },

  // Обновление DOM-элемента
  'content.update': function(e, param) {
    e.preventDefault()

    var el = $(e.target)

    el.trigger('content.update.prepare')

    var url = el.is('a') ? el.attr('href') : false
    if (url) {
      $.get(url, function(result, status, x) {
        var target = null == el.data('target') ? el : $(el.data('target'))
        if (target) {
          if ('append' == el.data('update')) {
            target.append(result.data.content)
          }
          else {
            target.replaceWith(result.data.content)
          }

          el.trigger('content.update.success', [result])
        }
      }, 'json')
    }
  },

  // Открытие модального окна
  'window.open': function(e, param) {
    e.preventDefault()

    var el = $(e.target)

    var href = el.attr('href') + (-1 != el.attr('href').indexOf('?') ? '&' : '?') + 'frame=true'

    $.colorbox({
      href: href,
      iframe: true,
      scrolling: false,
      transition: 'elastic',
      speed: 250,
      initialWidth: 1,
      initialHeight: 1,
      onClosed: function() {
        if (el.data('reload')) {
          window.location.reload()
        }
      }
    })
  },

  // Отправка формы
  'form.submit': function(e, param) {
    var el = $(e.target)

    if (window.isInFrame()) {
      el.attr('action', el.attr('action') + (-1 != el.attr('action').indexOf('?') ? '&' : '?') + 'frame=true&reload-parent='+el.data('reload'))
    }
  },

  // Ajax-отправка формы
  'form.ajax-submit': function(e, param) {
    e.preventDefault()

    var el = $(e.target)

    el.ajaxSubmit({
      success: function(result) {
        var target = el.data('target') ? $(el.data('target')) : el

        target.replaceWith(result.data.content);

        el.trigger('form.ajax-submit.success', [result])
      }
    })
  }
}



$(document).ready(function() {

  // Настройки colorbox
  $.extend($.colorbox.settings, {
    opacity: 0.7,
    fixed: false,
    close: 'закрыть <span style="font: bold 16px Verdana">&times;</span>'
  })

  // Обработчики ajax-ошибок
  $(document).ajaxError(function(e, x, settings, exception) {
    $.extend($.colorbox.settings, {
      width: 400,
      height: 200
    })

    if (x.status == 0) {
      $.colorbox({html: '<h1>Ошибка</h1><br />Не удается подключиться к серверу'})
    } else if (x.status == 401) {
      EventHandler.trigger(e, 'secure')
    } else if (x.status == 404) {
      $.colorbox({html: '<h1>Ошибка 404</h1><br />Запрашиваемая страница не найдена'})
    } else if (x.status == 403) {
      $.colorbox({html: '<h1>Ошибка 403</h1><br />Время сессии пользователя истекло. Авторизуйтесь заново, пожалуйста'})
    } else if (x.status == 500) {
      $.colorbox({html: '<h1>Ошибка 500</h1><br />Ошибка сервера'})
    } else if (e == 'parsererror') {
      $.colorbox({html: '<h1>Ошибка</h1><br />Не удалось обработать ответ сервера'})
    } else if (e == 'timeout') {
      $.colorbox({html: '<h1>Ошибка</h1><br />Время ожидания ответа истекло'})
    } else {
      $.colorbox({html: '<h1>Ошибка</h1><br />Неизвестная ошибка.' + "\n" + x.responseText})
    }
  })

  // Если окно находится во фрейме, то изменить размер модального окна
  if (window.isInFrame()) {
    window.parent.$.fn.colorbox.resize({innerHeight: $(document).height(), innerWidth: $(document).width()})
  }

  // Подключение стандартных обработчиков
  $.each({
    '.event-click': 'click',
    '.event-submit': 'submit'
  }, function(selector, eventName) {
    $(selector).live(eventName, function(e) {
      EventHandler.trigger(e)
    })
  })

})