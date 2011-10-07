
<?php include_partial('product', array('product' => $product)) ?>


<!-- Response -->
<h2 class="bold">Отзывы пользователей (<?php echo count($list)?>)</h2>
<div class="line pb15"></div>
<table class="gradetable fl ml20">
<thead>
	<tr>
		<th>Средняя оценка:</th>
		<td class="font18"><img src="/css/skin/img/stars3.png" alt="" width="113" height="21" class="vm mr10" /><?php echo round($product->rating, 1) ?></td>
	</tr>
</thead>
<tbody>
	<tr>
		<th><a href="javascript:void(0)">5 звезд</a></th>
		<td><div class="grade"><div style="width:80%"></div></div>34</td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">4 звезды</a></th>
		<td><div class="grade"><div style="width:60%"></div></div>22</td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">3 звезды</a></th>
		<td><div class="grade"><div style="width:40%"></div></div>15</td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">2 звезды</a></th>
		<td><div class="grade"><div style="width:20%"></div></div>7</td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">1 звезда</a></th>
		<td><div class="grade"><div style="width:10%"></div></div>5</td>
	</tr>
</tbody>
</table>

<table class="gradetable fr mr20">
<thead>
	<tr>
		<th>Лучшая оценка:</th>
		<td class="font18">Дизайн</td>
	</tr>
</thead>
<tbody>
	<tr>
		<th>Дизайн</th>
		<td><img src="/css/skin/img/grade.png" alt="" width="150" height="8" /> 34</td>
	</tr>
	<tr>
		<th>Надежность</th>
		<td><img src="/css/skin/img/grade.png" alt="" width="150" height="8" /> 22</td>
	</tr>
	<tr>
		<th>Удобство</th>
		<td><img src="/css/skin/img/grade.png" alt="" width="150" height="8" /> 15</td>
	</tr>
	<tr>
		<th>Быстродействие</th>
		<td><img src="/css/skin/img/grade.png" alt="" width="150" height="8" /> 7</td>
	</tr>
</tbody>
</table>

<div class="recomendbox">
	<?php $recomendStat = $product->getRecomendStat() ?>
	<div class="pb10">Рекомендации покупателей: <b class="percentbox"><?php echo $recomendStat['percent'] ?>%</b></div>
	<?php echo $recomendStat['recomends'] ?> из <?php echo $recomendStat['count'] ?> покупателей рекомендуют этот<br />продукт своим друзьям
</div>
<!-- /Response  -->

<div class="clear"></div>

<div class="fl mr20">Есть мнение по данному товару?<br />Напиши свой отзыв</div><a href="<?php echo url_for('productComment_new', $sf_data->getRaw('product')) ?>" class="button bigbuttonlink">Написать отзыв</a>
<div class="clear pb15"></div>

		<!-- Filter -->
		<div class="filter">
			<span class="fl">Сортировать по:</span>
			<div class="filterchoice">
				<a href="" class="filterlink">Рейтингу</a>
				<div class="filterlist">
					<a href="" class="filterlink">Рейтингу</a>
					<ul>
						<li><a href="">Цене (убывание)</a></li>
						<li><a href="">Цене (возрастание)</a></li>
						<li><a href="">Новинкам</a></li>
						<li><a href="">Наличию</a></li>
					</ul>
				</div>
			</div>
		</div> 
		<!-- /Filter -->


		<!-- Pageslist -->
		<div class="pageslist">
			<span>Страницы:</span>
			<ul>
				<li><a href="">1</a></li>
				<li><a href="">2</a></li>
				<li class="current"><a href="">3</a></li>
				<li><a href="">4</a></li>
				<li><a href="">5</a></li>
				<li class="next"><a href="">...29</a></li>
			</ul>
		</div>
		<!-- /Pageslist -->
		<div class="line pb15"></div>


<!-- Comments -->
<?php foreach ($list as $item): ?>
<div class="comment">
	<div class="commentleft">
		<div class="avatar"><img src="/css/skin/img/avatar.png" alt="" width="54" height="54" /></div>
		<?php echo $item->getUser() ?><br /><span class="gray"><?php echo $item->created_at ?></span><br /><br />Полезный отзыв?
		<div class="vote"><a href="" class="green">Да</a> <?php echo $item->helpful ?> / <a href="">Нет</a> <?php echo $item->unhelpful ?></div>
	</div>
	<div class="commentright">
		<div class="commenttext">
		   <span class="ratingview"></span>
			<?php echo $item->content ?>
		</div>

		<ul class="gradelist">
			<?php foreach ($item->getProductRating() as $rate): ?>
			<li>
				<div><?php echo $rate['Property']['name'] ?></div>
				<div class="ratingscale">
					<div class="ratingscalebox">
						<div class="current" style="width: <?php echo floor($rate['value'])*30 ?>px;"></div>
					</div>
				</div>
                <div class="ratingscaleresult"><?php echo round($rate['value'], 3) ?></div>
			</li>
			<?php endforeach ?>
		</ul>

		<div class="clear"></div>
		
		<?php $comments = $item->getSubComments() ?>
		<a href="javascript:void(0)" class="subcomments-trigger commentlink">Комментарии (<?php echo count($comments)?>)</a>
		<div class="commentanswer">
			<?php foreach ($comments as $comment): ?>
			<div class="answer">
				<div class="answerleft">
					<div class="avatar">.....&nbsp;&nbsp;<img src="/css/skin/img/avatar.png" alt="" width="54" height="54" /></div>
					<?php echo $comment->getUser() ?><br /><span class="gray"><?php echo $comment->created_at ?></span>
				</div>
				<div class="answerright"><?php echo $comment->content ?></div>
			</div>
			<?php endforeach ?>
		</div>

		<div class="clear"></div>

		<div class="sharebox">
			<a href="" class="sharelink">Поделиться</a>
			<div class="sharelist">

				<ul>
					<li><a href="" class="facebook">Facebook</a></li>
					<li><a href="" class="vkontakte">Вконтакте</a></li>
					<li><a href="" class="mailru">Mail.ru</a></li>
					<li><a href="" class="odnoklassniki">Одноклассники</a></li>
					<li><a href="" class="twitter">Twitter</a></li>
				</ul>
			</div>
		</div>
		
		<a href="javascript:void(0)" class="button whitelink subcomments-add-trigger">Написать комментарий</a>
		<div class="addcomment">
			<div class="pb10">Текст сообщения</div>
			<div class="textareabox"><textarea rows="4" cols="125">Очень хороший аппарат</textarea></div>
			<input type="button" class="button yellowbutton" value="Отправить" />    
		</div>    
	</div>
</div>
<?php endforeach ?>
<!-- /Comments  -->

 <!-- Pageslist -->
		<div class="pageslist fr">
			<span>Страницы:</span>
			<ul>
				<li><a href="">1</a></li>
				<li><a href="">2</a></li>
				<li class="current"><a href="">3</a></li>
				<li><a href="">4</a></li>
				<li><a href="">5</a></li>
				<li class="next"><a href="">...29</a></li>
			</ul>
		</div>
<!-- /Pageslist -->

<div class="fl mr20">Есть мнение по данному товару?<br />Напиши свой отзыв</div><a href="<?php echo url_for('productComment_new', $sf_data->getRaw('product')) ?>" class="button bigbuttonlink">Написать отзыв</a>
<div class="clear pb15"></div>


<div class="clear"></div>