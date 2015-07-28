<?php 
/**
 * @var $list stdClass 
 * @var $title string
 * @var $filter bool  активация панели фильтрации
 * @var $pagination bool  активация указателя страниц
 * @var $route string текущий маршрут
 * @var $page \View\Shop\ShowPage
 */


/**
 * Формирование GET параметров для маршрутизатора
 * @param stdClass $list - объект ответа от бэкэнда
 * @param array $params - массив параметров для маршрута
 * @return array
 */
if(!function_exists('routeParams')) {
	function routeParams ($list,array $params=[]){
		if(isset($params['page']) && $params['page']==0)
			unset($params['page']);
		elseif(!isset($params['page']) && $list->page>0)
			$params['page']	= $list->page;

		if(!isset($params['order']))
			$params['order'] = $list->order;
		return $params;
	}
}
?>


<?php if(!empty($list)): ?>
	<a name="<?=$id?>"></a>
	<?=($title?'<h2>'.$title.'</h2>':null)?>

	<?php if ($filter): ?>
	<div class="bSortingLine clearfix js-category-sortingAndPagination">
		<?=$page->render('photocontest/_repostCode')?>
		<ul class="bSortingList mSorting js-category-sorting">
			<li class="bSortingList__eItem mTitle">Показать сначала:</li>
			<li class="bSortingList__eItem mSortItem<?=$list->order==='d'?' mActive js-category-sorting-activeItem':null?> js-category-sorting-item">
				<a href="<?=\App::router()->generate($route, routeParams($list, ['order'=>'d']))?>#<?=$id?>" class="bSortingList__eLink js-category-sorting-link">
					Свежие
				</a>
			</li>
			<li class="bSortingList__eItem mSortItem<?=$list->order==='r'?' mActive js-category-sorting-activeItem':null?> js-category-sorting-item">
				<a href="<?=\App::router()->generate($route,routeParams($list, ['order'=>'r']))?>#<?=$id?>" class="bSortingList__eLink js-category-sorting-link">
					Популярные
				</a>
			</li>
		</ul>
	</div>
	<?php endif; ?>

	<ul class="pc_tail">
		<?php foreach ($list->items as $v): ?>
		<li>
			<a href="<?=\App::router()->generate('pc.photo.show',['id'=>$v->id,'contestRoute'=>$contest->route])?>" class="pc_photo" title="<?=$v->name?>">
				<div class="pc_date"><?=date('d.m.Y H:i',$v->udCreate)?></div>
				<div class="<?=$contest->voteEnabled?'__vote ':'disabled '?> pc_vote<?=$v->vote?' active':null?>" data-id="<?=$v->id?>"><i><?=$v->meta->voteCounter?$v->meta->voteCounter:0?></i></div>
				<img src="<?=$v->fileUrlPreview?>" title="<?=$v->name?>"/>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>



	<?php 
	/**
	 * Пока попроще делаем, ну в баню эти коллекции и прочее
	 * времени нет
	 */
	if($pagination && $list->total>$list->limit): 
	?>
	<div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
		<div class="bSortingList mPager js-category-pagination">
			<ul class="bSortingList">
				<li class="bSortingList__eItem mTitle">Страницы</li>

				<?php for($i=0,$k=0; $i<$list->total; $i+=$list->limit,$k++): ?>
				<li class="bSortingList__eItem mPage<?=$list->page==$k?' mActive':null?> js-category-pagination-page">
					<?php if($k>0):?>
					<a href="<?=\App::router()->generate($route,routeParams($list, ['page'=>$k]))?>#<?=$id?>" class="bSortingList__eLink js-category-pagination-page-link"><?=($k+1)?></a>
					<?php else: ?>
					<a href="<?=\App::router()->generate($route,routeParams($list))?>#<?=$id?>" class="bSortingList__eLink js-category-pagination-page-link">1</a>
					<?php endif; ?>
				</li>
				<?php endfor; ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>

<?php endif; ?>