<?php
/**
 * @var $request \Http\Request 
 */
?>
<div class="pc_head">
	<h1><?=$contest->name?></h1>
</div>
<div id="pc_splash">
	<a class="pc_button mBtnOrange" href="<?=\App::router()->generateUrl('pc.photo.create',['contestRoute'=>$contest->route])?>">Загрузить фото</a>
</div>
<p class="pc_info"><?=$contest->annot?></p>

<?=\App::templating()->render('photocontest/_list-tail', array(
	'id'		=> 'top',
	'title'		=> 'Лидеры конкурса',
	'list'		=> $top,
	'filter'	=> false,
	'pagination'=> false,
	'contest'	=> $contest,
    'page'      => $page
));?>


<?=\App::templating()->render('photocontest/_list-tail', array(
	'id'		=> 'tail',
	'title'		=> 'Все фотографии',
	'list'		=> $list,
	'filter'	=> true,
	'pagination'=> true,
	'route'		=> 'pc.homepage',
	'contest'	=> $contest,
    'page'      => $page
));?>




