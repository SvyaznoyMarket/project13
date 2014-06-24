<?php
/**
 * @var $request \Http\Request 
 */
?>
<a href="<?=\App::router()->generate('pc.photo.create',['contestRoute'=>$contest->route])?>" class="pc_button pc_right">Загрузить фото</a>
<div class="pc_head">
	<h1><?=$contest->name?></h1>
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




