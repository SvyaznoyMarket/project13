<script src="http://vkontakte.ru/js/api/openapi.js?3"></script>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/ru_RU/all.js"></script>

<link href="http://www.odnoklassniki.ru/oauth/resources.do?type=css" rel="stylesheet" />
<script src="http://www.odnoklassniki.ru/oauth/resources.do?type=js" type="text/javascript charset=utf-8"></script>

<script type="text/javascript" src="http://cdn.connect.mail.ru/js/loader.js"></script>

<ul id="open_auth-block" class="inline">
<?php foreach ($list as $item): ?>
  <li><a id="open_auth_<?php echo $item['token'] ?>-link" class="open_auth-link" href="<?php echo $item['url'] ?>" <?php foreach($item['data'] as $k => $v) echo 'data-'.$k.'="'.$v.'"' ?> target="_blank"><?php echo $item['name'] ?></a></li>
<?php endforeach ?>
</ul>