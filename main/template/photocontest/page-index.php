<?php
/**
 * @var $request \Http\Request 
 */
?>
<div class="pc_head">
	<h1><?=$contest->name?></h1>
</div>
<div id="pc_splash">
	<a class="pc_button mBtnOrange" href="<?=\App::router()->generate('pc.photo.create',['contestRoute'=>$contest->route])?>">Загрузить фото</a>
</div>
<p class="pc_info"><?=$contest->annot?></p>

<?=\App::templating()->render('photocontest/_list-tail', array(
	'title'		=> 'Лидеры конкурса',
	'list'		=> $top,
	'filter'	=> false,
	'pagination'=> false,
	'contest'	=> $contest
));?>


<?=\App::templating()->render('photocontest/_list-tail', array(
	'title'		=> 'Все фотографии',
	'list'		=> $list,
	'filter'	=> true,
	'pagination'=> true,
	'route'		=> 'homepage',
	'contest'	=> $contest
));?>




