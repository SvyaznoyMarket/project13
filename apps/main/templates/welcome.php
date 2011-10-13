<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Enter.ru</title>
<meta name="description" content="" />
<meta name="keywords" content="" />
<link href="/css/skin/entry.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="allpage">


    <div class="entry">
        <div class="entrybox">
        <form action="<?php echo url_for('@welcome') ?>" method="post">
          <input type="text" class="text" value="Введи секретное слово :)"  onfocus="if (this.value == 'Введи секретное слово :)') this.value = '';" onblur="if (this.value == '') this.value = 'Введи секретное слово :)';" name="<?php echo sfConfig::get('app_welcome_cookie_name') ?>" />
            <input type="submit" class="entrybutton" value="Войти" />
        </form>
        </div>
    </div>

</div>

</body>
</html>
