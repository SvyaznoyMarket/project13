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


// Документ готов
$(document).ready(function() {

  // Настройки colorbox
  $.extend($.colorbox.settings, {
    opacity: 0.7,
    fixed: true,
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


  // Специализированные обработчики
  $('.product_rating-form').live({
    'form.ajax-submit.prepare': function(e, result) {
      $(this).find('input:submit').attr('disabled', true)
    },
    'form.ajax-submit.success': function(e, result) {
      if (true == result.success) {
        $('.product_rating-form').effect('highlight', {}, 2000)
      }
    }
  })

  $('.product_comment-form').live({
    'form.ajax-submit.prepare': function(e, result) {
      $(this).find('input:submit').attr('disabled', true)
    },
    'form.ajax-submit.success': function(e, result) {
      $(this).find('input:submit').attr('disabled', false)
      if (true == result.success) {
        $($(this).data('listTarget')).replaceWith(result.data.list)
        $.scrollTo('.' + result.data.element_id, 500, {
          onAfter: function() {
            $('.' + result.data.element_id).effect('highlight', {}, 2000);
          }
        })
      }
    }
  })

  $('.product_comment_response-link').live({
    'content.update.prepare': function(e) {
      $('.product_comment_response-block').html('')
    },
    'content.update.success': function(e) {
      $('.product_comment_response-block').find('textarea:first').focus()
    }
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


  $('.order-form').bind({
    'change': function(e, effect) {

      var form = $(this)
      var hidden = []

      if (undefined == effect) {
        effect = true
      }

      // если способ получения не доставка
      var el = form.find('[name="order[receipt_type]"]:checked')
      if (!el.length || ('delivery' != el.val())) {
        hidden.push(
          'delivery_type_id',
          'delivered_at',
          'address'
        )
      }
      if (!el.length || ('pickup' != el.val())) {
        hidden.push(
          'shop_id'
        )
      }

      function checkPersonType() {
        var d = $.Deferred();

        // если изменился тип лица (юридическое, физическое)
        if ('order[person_type]' == $(e.target).attr('name')) {
          var el = form.find('[name="order[person_type]"]:checked')
          if (el.length) {
            effect = $('.form-row[data-field="delivery_type_id"]').is(':visible')

            $.post(form.data('updateFieldUrl'), {
              order: {
                person_type: el.val()
              },
              field: 'delivery_type_id'
            }, function(result) {
              if (true === result.success) {
                $('.form-row[data-field="delivery_type_id"] .content').hide('fast', function() {
                  $('.form-row[data-field="delivery_type_id"]').replaceWith(result.data.content)
                  d.resolve()
                })
              }
              else {
                d.reject()
              }
            }, 'json')
            .error(function() {
              d.reject()
            })
          }
        }
        else {
          d.resolve()
        }

        return d.promise();
      }

      $.when(checkPersonType())
      .then(function() {
        form.find('.form-row').each(function(i, el) {
          var el = $(el)

          if (-1 == $.inArray(el.data('field'), hidden)) {
            if (true == effect) {el.show('fast')} else {el.show()}
          }
          else {
            if (true == effect) {el.hide('fast')} else {el.hide()}
          }
        })
      })

    }
  })

  $('.order_user_address').bind('change', function(e) {
    var el = $(this)

    $('[name="order[address]"]').val(el.val())
  })

  $('.order-form').trigger('change', [false])

})