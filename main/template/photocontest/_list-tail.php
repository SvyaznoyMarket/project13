<?php 
/**
 * @var $list stdClass 
 * @var $title string
 * @var $filter bool  активация панели фильтрации
 * @var $pagination bool  активация указателя страниц
 * @var $route string текущий маршрут
 */


/**
 * Формирование GET параметров для маршрутизатора
 * @param stdClass $list - объект ответа от бэкэнда
 * @param array $params - массив параметров для маршрута
 * @return array
 */
if(!function_exists('routeParams')) {
	function routeParams ($list,array $params=[]){
		if($params['page']==0)
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

	<?=($title?'<h2>'.$title.'</h2>':null)?>

	<?php if ($filter): ?>
	<div class="bSortingLine clearfix">
		<!-- Button BEGIN -->
		<script type="text/javascript">(function() {
		  if (window.pluso)if (typeof window.pluso.start == "function") return;
		  if (window.ifpluso==undefined) { window.ifpluso = 1;
			var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
			s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
			s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
			var h=d[g]('body')[0];
			h.appendChild(s);
		  }})();</script>
		<div class="pluso pc_buttons" data-background="transparent" data-options="small,square,line,horizontal,nocounter,theme=08" data-services="facebook,vkontakte,odnoklassniki,twitter,email"></div>
		<!-- Button END -->
		
		<ul class="bSortingList mSorting">
			<li class="bSortingList__eItem mTitle">Показать сначала:</li>
			<li class="bSortingList__eItem mSortItem<?=$list->order==='d'?' mActive':null?>">
				<a href="<?=\App::router()->generate($route, routeParams($list, ['order'=>'d']))?>" class="bSortingList__eLink jsSorting">
					Свежие
				</a>
			</li>
			<li class="bSortingList__eItem mSortItem<?=$list->order==='r'?' mActive':null?>">
				<a href="<?=\App::router()->generate($route,routeParams($list, ['order'=>'r']))?>" class="bSortingList__eLink jsSorting">
					Популярные
				</a>
			</li>
		</ul>
	</div>
	<?php endif; ?>

	<ul class="pc_tail">
		<?php foreach ($list->items as $v): ?>
		<li>
			<a href="<?=\App::router()->generate('pc.photo.show',['id'=>$v->id,'contestId'=>$v->contestId])?>" class="pc_photo" title="<?=$v->title?>">
				<div class="pc_date"><?=date('d.m.Y H:i',$v->udCreate)?></div>
				<div class="<?=$contest->setup->voteEnabled?'__vote ':'disabled '?> pc_vote<?=$v->vote?' active':null?>" data-id="<?=$v->id?>"><i><?=$v->meta->voteCounter?$v->meta->voteCounter:0?></i></div>
				<img src="<?=$v->fileUrlPreview?>" title="<?=$v->title?>"/>
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
	<div class="bSortingLine mPagerBottom clearfix">
		<div class="bSortingList mPager">
			<ul class="bSortingList">
				<li class="bSortingList__eItem mTitle">Страницы</li>

				<?php for($i=0,$k=0; $i<$list->total; $i+=$list->limit,$k++): ?>
				<li class="bSortingList__eItem mPage<?=$list->page==$k?' mActive':null?>">
					<?php if($k>0):?>
					<a href="<?=\App::router()->generate($route,routeParams($list, ['page'=>$k]))?>" class="bSortingList__eLink jsPagination"><?=($k+1)?></a>
					<?php else: ?>
					<a href="<?=\App::router()->generate($route,routeParams($list))?>" class="bSortingList__eLink jsPagination">1</a>
					<?php endif; ?>
				</li>
				<?php endfor; ?>
			</ul>
		</div>
	</div>
	<?php endif; ?>

<?php endif; ?>