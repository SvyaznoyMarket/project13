$(document).ready(function() {

  // init odnoklassniki
  $('#open_auth_odnoklassniki-link').addClass('odkl-oauth-lnk')


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
    },
    window: function(e, param) {
      window.location = param.url
    }
  })

  // vkontakte
  $.when($.getScript('http://vkontakte.ru/js/api/openapi.js?3'))
  .done(function() {
    // init vkontakte
    VK.init({
      apiId: $('#open_auth_vkontakte-link').data('appId')
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

    $('#open_auth_vkontakte-link').show('fast')
  })

  // facebook
  $.when($.getScript('http://connect.facebook.net/ru_RU/all.js'))
  .done(function() {
    FB.init({
        appId: $('#open_auth_facebook-link').data('appId'),
        status: true,
        cookie: true,
        xfbml: false
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

    $('#open_auth_facebook-link').show('fast')
  })


  // twitter
  $('#open_auth_twitter-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(e.target)

    function login() {
      var d = $.Deferred()

      $.post(el.attr('href'), function(result) {
        if (true === result.success) {
          window.location = result.data.url
        }
        else {
          d.reject()
        }
      })
      .error(function() {
        d.reject()
      })

      return d.promise()
    }

    login()
  })
  $('#open_auth_twitter-link').show('fast')

  // mailru
  $.when($.getScript('http://cdn.connect.mail.ru/js/loader.js'))
  .done(function() {
    /*
    $('#open_auth_mailru-link').bind('click', function(e) {
      e.preventDefault()

      var el = $(e.target)

      function login() {
        window.open(el.data('url'), 'mailruWindow', 'status = 1, width = 542, height = 420')
      }

      login()
    })
    */
    mailru.loader.require('api', function() {
      mailru.connect.init($('#open_auth_mailru-link').data('appId'), $('#open_auth_mailru-link').data('privateKey'))

      mailru.events.listen(mailru.connect.events.login, function(response) {
        if (response.session_key) {
          window.location = $('#open_auth_mailru-link').data('signinUrl')
        }
        else {
          $('#open_auth-block').trigger('unauthorized')
        }
      })

      var background = $('#open_auth_mailru-link').css('background')
      $('#open_auth_mailru-link')
        .data('signinUrl', $('#open_auth_mailru-link').attr('href'))
        .addClass('mrc__connectButton')
      mailru.connect.initButton()
      $('#open_auth_mailru-link').css('background', background)

      $('#open_auth_mailru-link').show('fast')
    })
  })

  $('#open_auth_odnoklassniki-link').bind('click', function(e) {
    e.preventDefault()

    var el = $(e.target)

    function login() {
      ODKL.Oauth2(e.target, el.data('appId'), 'VALUABLE ACCESS', el.data('returnUrl') )
    }

    login()
  })
  $('#open_auth_odnoklassniki-link').show('fast')

})