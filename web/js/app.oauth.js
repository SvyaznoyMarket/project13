$(document).ready(function() {

  VK.init({
    apiId: $('#open_auth_vkontakte-link').data('appId')
  })

  $('#open_auth-block').bind({
    unauthorized: function(e) {
      $.colorbox({
        html: '<h1>Вход</h1><p>Произошла ошибка при авторизации.<br />Пожалуйста, повторите попытку.</p>'
      })
    },
    quickRegister: function(e, param) {
      $.colorbox({
        href: param.url+'?frame=true',
        iframe: true,
        scrolling: false,
        transition: 'elastic',
        speed: 250,
        initialWidth: 1,
        initialHeight: 1,
        onClosed: function() {
        }
      })
    },
    signin: function(e, param) {
      window.location = param.url
    }
  })

  $('#open_auth_vkontakte-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(e.target)

    function login() {
      var d = $.Deferred()

      VK.Auth.login(function(response) {
        if (response.session) {
          d.resolve()
        } else {
          d.reject()
        }
      }, VK.access.FRIENDS)

      return d.promise()
    }

    $.when(login())
    .done(function(data) {

      $.post(el.attr('href'), {
        data: data
      }, function(result) {
        if (true == result.success) {
          if (typeof result.data.action != 'undefined') {
            $('#open_auth-block').trigger(result.data.action, [result.data.param])
          }
        }
      }, 'json')

    })
    .fail(function() {
      $('#open_auth-block').trigger('unauthorized')
    })

  })

})