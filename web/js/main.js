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
      el.oneTime(10000, function() { $(this).remove() })
      el.find('a').bind('click', function() { $(this).parent().remove() })

      $('body').append(el);
    }

    return true;
  }
})


// Обработчик событий
EventHandler = {
  'trigger': function(e) {
    var el = $(e.target)
    var name = el.data('event')

    $.log('Event ' + name + ' fired')
    if (typeof this[name] == 'function')
    {
      this[name](e, el, el.data())
    }
  },

  // Обновление DOM-элемента
  'content.update': function(e, el, data) {
    e.preventDefault()

    var url = el.is('a') ? el.attr('href') : false
    if (url) {
      $.get(url, function(result, status, x) {
        var target = null == data.target ? el : $('#' + data.target)
        if (target) {
          target.replaceWith(result.data)
        }
      }, 'json')
    }
  },

  // Открытие модального окна
  'window.open': function(e, el, data) {
    e.preventDefault()

    var href = el.attr('href') + (-1 != el.attr('href').indexOf('?') ? '&' : '?') + 'frame=true'

    $.colorbox({
      href: href,
      iframe: true,
      scrolling: false,
      transition: 'elastic',
      speed: 250,
      initialWidth: 1,
      initialHeight: 1
    })
  },

  // Отправка формы
  'form.submit': function(e, el, data) {
    el.attr('action', el.attr('action') + (-1 != el.attr('action').indexOf('?') ? '&' : '?') + 'frame=true')
  },

  // Ajax-отправка формы
  'form.ajax-submit': function(e, el, data) {
    e.preventDefault()

    el.ajaxSubmit()
  }
}


// Документ готов
$(document).ready(function() {

  // Если окно находится во фрейме, то изменить размер модального окна
  if (window.isInFrame()) {
    window.parent.$.fn.colorbox.resize({innerHeight: $(document).height(), innerWidth: $(document).width()})
  }

  // Подключение обработчиков
  $.each({
    '.event-click': 'click',
    '.event-submit': 'submit'
  }, function(selector, eventName) {
    $(selector).live(eventName, function(e) {
      EventHandler.trigger(e)
    })
  })


  $('.product_filter-block')
    // change
    .bind('change', function(e) {
      var el = $(e.target)

      if (el.is('input') && (-1 != $.inArray(el.attr('type'), ['radio', 'checkbox']))) {
        el.trigger('preview')
        return false
      }
    })
    // preview
    .bind('preview', function(e) {
      var el = $(e.target)
      var form = $(this)

      function disable() {
        var d = $.Deferred();
        //el.attr('disabled', true)
        return d.resolve();
      }

      function enable() {
        var d = $.Deferred();
        //el.attr('disabled', false)
        return d.promise();
      }

      function getData() {
        var d = $.Deferred();

        form.ajaxSubmit({
          url: form.data('action-count'),
          success: d.resolve,
          error: d.reject
        })

        return d.promise();
      }

      $.when(getData())
        .then(function(result) {
          if (true === result.success) {
            $('.product_count-block').remove();
            el.parent().find('> label').first().after('<div class="product_count-block" style="position: absolute; background: #fff; padding: 4px; opacity: 0.9; border-radius: 5px; border: 1px solid #ccc; cursor: pointer;">Найдено '+result.data+'</div>')
            $('.product_count-block')
              .hover(
                function() {
                  $(this).stopTime('hide')
                },
                function() {
                  $(this).oneTime(2000, 'hide', function() {
                    $(this).remove()
                  })
                }
              )
              .click(function() {
                form.submit()
              })
              .trigger('mouseout')
          }
        })
        .fail(function(error) {})
    })

})