<?php
/**
 * @var $request \Http\Request 
 */
?>

<h1><?=$contest->name?></h1>
<p><?=$contest->annot?></p>

<?=\App::templating()->render('photocontest/_list-tail', array(
	'title'		=> 'Лидеры конкурса',
	'list'		=> $top,
	'filter'	=> false,
	'pagination'=> false
));?>


<?=\App::templating()->render('photocontest/_list-tail', array(
	'title'		=> 'Все фотографии',
	'list'		=> $list,
	'filter'	=> true,
	'pagination'=> true,
	'route'		=> 'homepage'
));?>




