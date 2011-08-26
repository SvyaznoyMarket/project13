$(document).ready(function() {

  VK.init({
    apiId: $('#open_auth_vkontakte-link').data('appId')
  })

  FB.init({
      appId: $('#open_auth_facebook-link').data('appId'),
      status: true,
      cookie: true,
      xfbml: false
  });

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
      $.when(param.login())
      .done(function() {

        $.post(param.url, {
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
    },
    reload: function(e, param) {
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

    $('#open_auth-block').trigger('signin', [{
      login: login,
      url: el.attr('href')
    }])
  })


  $('#open_auth_facebook-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(e.target)

    function login() {
      var d = $.Deferred()

      FB.login(function(response) {
        if (response.session) {
          d.resolve()
        } else {
          d.reject()
        }
      }, {
        perms: 'user_birthday,user_location,email'
      })

      return d.promise()
    }

    $('#open_auth-block').trigger('signin', [{
      login: login,
      url: el.attr('href')
    }])
  })

})