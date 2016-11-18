<a href="<?=\App::router()->generateUrl('pc.photo.create',['contestRoute'=>$contest->route])?>" class="pc_button pc_right">Участвовать</a>
<div class="pc_head pc_left">
	<?=\App::closureTemplating()->render('/__breadcrumbs', ['links' => $breadcrumbs]) ?>
	<h1><?=$item->name?></h1>
</div>
<div class="clear"></div>

<ul id="pc_photostream" class="clearfix">
	<li class="pc_stream-nav pc_prev"><i></i></li>
	<li class="pc_stream-nav pc_next"><i></i></li>
	<li class="pc_stream-wrapper">
		<ul class="pc_photostream">
			<?php 
			foreach ($list->items as $v): 
			$url = \App::router()->generateUrl('pc.photo.show',['id'=>$v->id,'contestRoute'=>$contest->route]);	
			?>
			<li<?=($v->id==$item->id)?' class="selected"':null?>>
				<a href="<?=$url?>" title="<?=$v->name?>">
					<img alt="<?=$v->name?>" src="<?=$v->fileUrlIcon?>">
				</a>
			</li>
			<?php endforeach; ?>
		</ul>
	</li>
</ul>


<div class="pc_photo">
	<div class="pc_date"><?=date('d.m.Y H:i',$item->udCreate)?></div>
	<div class="<?=$contest->voteEnabled?'__vote ':'disabled '?>pc_vote<?=$item->vote?' active':null?>" data-id="<?=$item->id?>"><i><?=$item->meta->voteCounter?$item->meta->voteCounter:0?></i></div>
	<img src="<?=$item->fileUrlView?>" title="<?=$item->name?>"/>
</div>

<?=$page->render('photocontest/_repostCode')?>

<h2 class="pc_left">Вы можете оставить свой комментарий к этой фотографии</h2>

 <!--Комментарии вконтакте--> 
<script type="text/javascript" src="//vk.com/js/api/openapi.js?113"></script>
<script type="text/javascript">VK.init({apiId: <?=(App::config()->debug?4432950:4432944)?>, onlyWidgets: true}); //production</script>

<!-- Комментарии facebook -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&appId=<?=(App::config()->debug?291694327668627:291693587668701)?>&version=v2.0";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div id="comments-box">
	<div class="bCommentSn mVk pc_left" id="vk_comments"></div>
	<div class="bCommentSn mFb fb-comments" data-href="<?=$request->getSchemeAndHttpHost().'/'.$request->getRequestUri()?>" data-numposts="10" data-width="460"></div>
</div>
<script type="text/javascript">VK.Widgets.Comments("vk_comments", {limit: 10, width: "460", attach: false});</script>

<div class="clear"></div>