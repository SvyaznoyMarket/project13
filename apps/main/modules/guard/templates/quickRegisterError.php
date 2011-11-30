<?php echo slot('title', 'Вход') ?>

<p>Время сессии истекло. Попробуйте <a id="auth_again-link" href="#">авторизовать заново</a>.</p>

<script type="text/javascript">

$(document).ready(function() {

  $('#auth_again-link').bind('click', function(e) {
    e.preventDefault()
    $('#auth-link').click()
  })
})

</script>