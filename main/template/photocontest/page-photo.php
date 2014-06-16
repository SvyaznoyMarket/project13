<?=\App::closureTemplating()->render('/__breadcrumbs', ['links' => $breadcrumbs]) ?>
<h1><?=$item->title?></h1>

<?php 
/**
 * @todo поставить нормальный slider с ajax подгрузкой (если будет более 100 фоток???) и выверенной работой прокрутки (сейчас "вкручиваешься" в белое полотно)
 */
?>
<ul id="pc_photostream" class="clearfix">
	<li class="pc_stream-nav pc_prev"><i></i></li>
	<li class="pc_stream-nav pc_next"><i></i></li>
	<li class="pc_stream-wrapper">
		<ul class="pc_photostream">
			<?php 
			foreach ($list->items as $v): 
			$url = \App::router()->generate('pc.photo.show',['id'=>$v->id,'contestId'=>$v->contestId]);	
			?>
			<li<?=($v->id==$item->id)?' class="selected"':null?>>
				<a href="<?=$url?>" title="<?=$v->title?>">
					<img alt="<?=$v->title?>" src="<?=$v->fileUrlIcon?>">
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</li>
</ul>


<div class="pc_photo" title="<?=$item->title?>">
	<div class="pc_date"><?=date('d.m.Y H:i',$item->udCreate)?></div>
	<div class="pc_vote<?=$item->vote?' active':null?>" data-id="<?=$item->id?>"><i><?=$item->meta->voteCounter?$item->meta->voteCounter:0?></i></div>
	<img src="<?=$item->fileUrlView?>" title="<?=$item->title?>"/>
</div>