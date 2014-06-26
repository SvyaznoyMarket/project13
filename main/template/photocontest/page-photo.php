<a href="<?=\App::router()->generate('pc.photo.create',['contestRoute'=>$contest->route])?>" class="pc_button pc_right">Участвовать</a>
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
			$url = \App::router()->generate('pc.photo.show',['id'=>$v->id,'contestRoute'=>$contest->route]);	
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

<h2 class="pc_left">Вы можете оставить свой комментарий к этой фотографии</h2>

 <!--Комментарии вконтакте--> 
 <!-- Put this script tag to the <head> of your page -->
<script type="text/javascript" src="//vk.com/js/api/openapi.js?113"></script>
<?php if(App::config()->debug): ?>
<script type="text/javascript">VK.init({apiId: 4432950, onlyWidgets: true}); //production</script>
<?php else: ?> 
<script type="text/javascript">VK.init({apiId: 4432944, onlyWidgets: true}); // test </script>
<?php endif; ?>

<!-- Комментарии facebook -->
<div id="fb-root pc_right"></div>
<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

<div id="comments-box">
	<div class="bCommentSn mVk pc_left" id="vk_comments"></div>
	<div class="bCommentSn mFb fb-comments" data-href="<?=$request->getRequestUri()?>" data-numposts="10" data-width="460"></div>
</div>
<script type="text/javascript">VK.Widgets.Comments("vk_comments", {limit: 10, width: "460", attach: false});</script>

<div class="clear"></div>