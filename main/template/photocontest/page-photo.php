<a href="<?=\App::router()->generate('pc.photo.create',['contestId'=>$contest->id])?>" class="pc_button pc_right">Участвовать</a>
<div class="pc_head pc_left">
	<?=\App::closureTemplating()->render('/__breadcrumbs', ['links' => $breadcrumbs]) ?>
	<h1><?=$item->title?></h1>
</div>
<div class="clear"></div>

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


<div class="pc_photo">
	<div class="pc_date"><?=date('d.m.Y H:i',$item->udCreate)?></div>
	<div class="pc_vote<?=$item->vote?' active':null?>" data-id="<?=$item->id?>"><i><?=$item->meta->voteCounter?$item->meta->voteCounter:0?></i></div>
	<img src="<?=$item->fileUrlView?>" title="<?=$item->title?>"/>
</div>

<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style pc_right">
	<a class="addthis_button_facebook_like" fb:like:layout="button_count" style="display:block; float:left; width: 117px;"></a>
	<a class="addthis_button_tweet" style="width: 83px;"></a>
	<a class="addthis_button_pinterest_pinit" style="width: 50px;"></a>
	<a class="addthis_counter addthis_pill_style"></a>
</div>

<script src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1" type="text/javascript"></script>
<!-- AddThis Button END -->

<h2 class="pc_left">Вы можете оставить свой комментарий к этой фотографии</h2>

<!-- Комментарии вконтакте -->
<div class="bCommentSn mVk" id="vk_comments"></div>


<script type="text/javascript">
	VK.Widgets.Comments("vk_comments", {limit: 10, width: "460", attach: "*"});
</script>


<!-- Комментарии facebook -->
<div id="fb-root"></div>


<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>


<div id="photo-comments-box">
	<div class="bCommentSn mFb fb-comments" data-href="<?=$request->getRequestUri()?>" data-numposts="10" data-width="460"></div>
</div>

<div class="clear"></div>