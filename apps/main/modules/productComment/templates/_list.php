
<?php $recomendStat = $item->getRatingStat() ?>

<!-- Response -->
<h2 class="bold"><?php echo $item->name ?> - Отзывы покупателей (<?php echo count($list)?>)</h2>
<div class="line pb15"></div>
<table class="gradetable fl ml20">
<thead>
	<tr>
		<th>Средняя оценка:</th>
		<td class="font18"><img src="/css/skin/img/stars3.png" alt="" width="113" height="21" class="vm mr10" /><?php echo $recomendStat['rating_average'] ?></td>
	</tr>
</thead>
<tbody>
	<tr>
		<th><a href="javascript:void(0)">5 звезд</a></th>
		<td><div class="grade"><div style="width:<?php echo $recomendStat['count'] > 0 ? round($recomendStat['rating_5']/$recomendStat['count']*100) : 0 ?>%"></div></div><?php echo $recomendStat['rating_5'] ?></td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">4 звезды</a></th>
		<td><div class="grade"><div style="width:<?php echo $recomendStat['count'] > 0 ? round($recomendStat['rating_4']/$recomendStat['count']*100) : 0 ?>%"></div></div><?php echo $recomendStat['rating_4'] ?></td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">3 звезды</a></th>
		<td><div class="grade"><div style="width:<?php echo $recomendStat['count'] > 0 ? round($recomendStat['rating_3']/$recomendStat['count']*100) : 0 ?>%"></div></div><?php echo $recomendStat['rating_3'] ?></td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">2 звезды</a></th>
		<td><div class="grade"><div style="width:<?php echo $recomendStat['count'] > 0 ? round($recomendStat['rating_2']/$recomendStat['count']*100) : 0 ?>%"></div></div><?php echo $recomendStat['rating_2'] ?></td>
	</tr>
	<tr>
		<th><a href="javascript:void(0)">1 звезда</a></th>
		<td><div class="grade"><div style="width:<?php echo $recomendStat['count'] > 0 ? round($recomendStat['rating_1']/$recomendStat['count']*100) : 0 ?>%"></div></div><?php echo $recomendStat['rating_1'] ?></td>
	</tr>
</tbody>
</table>
<?php $grades = $item->getUsersRates() ?>
<?php if (count($grades) > 1): ?>
<table class="gradetable fr mr20">
<thead>
	<tr>
		<th>Лучшая оценка:</th>
		<td class="font18"><?php echo $grades[$grades['max_property_id']]['name'] ?></td>
	</tr>
</thead>
<tbody>
	<?php foreach ($grades as $k => $rate): ?>
	<?php if ($k == 'max_property_id') continue ?>
	<tr>
		<th><?php echo $rate['name'] ?></th>
		<td>
			<div class="ratingscale">
				<div class="ratingscalebox">
					<div class="current" style="width: <?php echo floor($rate['average'])*30 ?>px;"></div>
				</div>
			</div>
			<div class="ratingscaleresult"><?php echo $rate['value'] ?></div>
		</td>
	</tr>
	<?php endforeach ?>
</tbody>
</table>
<?php endif ?>

<div class="recomendbox">
	<div class="pb10">Рекомендации покупателей: <b class="percentbox"><?php echo $recomendStat['percent'] ?>%</b></div>
	<?php echo $recomendStat['recomends'] ?> из <?php echo $recomendStat['count'] ?> покупателей рекомендуют этот<br />продукт своим друзьям
</div>
<!-- /Response  -->

<div class="clear"></div>

<div class="fl mr20">Есть мнение по данному товару?<br />Напиши свой отзыв</div><a data-auth="<?php echo $sf_user->isAuthenticated() ? 'false' : 'true' ?>" href="<?php echo url_for('productComment_new', $sf_data->getRaw('product')) ?>" class="button bigbuttonlink">Написать отзыв</a>
<div class="clear pb15"></div>

		<?php if ($showSort !== false): ?>
		<!-- Filter -->
		<div class="filter">
			<span class="fl">Сортировать по:</span>
			<div class="filterchoice">
				<a href="<?php echo replace_url_for('sort', $sort) ?>" class="filterlink"><?php echo $sortParams[$sort]?></a>
				<div class="filterlist">
					<a href="<?php echo replace_url_for('sort', $sort) ?>" class="filterlink"><?php echo $sortParams[$sort]?></a>
					<ul>
						<?php foreach ($sortParams as $k => $label): ?>
						<?php if ($k == $sort) continue ?>
						<li><a href="<?php echo replace_url_for('sort', $k) ?>"><?php echo $label?></a></li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Filter -->
		<?php endif ?>

		<?php if ($showPage !== false): ?>
		<?php include_partial('pagination', array('pager' => $list, 'pos' => 'top')) ?>
		<?php else: ?>
		<strong style="float:right;margin-top:-40px;">
		<a href="<?php echo url_for('productComment', $sf_data->getRaw('product')) ?>" class="underline">Читать все отзывы</a>
		</strong>
		<?php endif ?>

		<div class="line pb15"></div>


<!-- Comments -->
<?php foreach ($list as $item): ?>
<div class="comment">
	<div class="commentleft">
		<div class="avatar"><img src="/css/skin/img/avatar.png" alt="" width="54" height="54" /></div>
		<?php echo $item->getUser() ?><br /><span class="gray"><?php echo $item->created_at ?></span><br /><br />Полезный отзыв?
		<div class="vote">
			<a href="javascript:void(0)" class="green">Да</a>
				<?php echo $item->helpful ?> /
			<a href="javascript:void(0)">Нет</a>
				<?php echo $item->unhelpful ?></div>
	</div>
	<div class="commentright">
		<div class="commenttext">
			<?php
				echo str_repeat('<span class="ratingview" style="width:13px;display:inline-block;"></span>', $item->rating);
				echo str_repeat('<span class="ratingview" style="width:13px;display:inline-block;background-position-x:100%;"></span>', 5-$item->rating);
			?>
			<br/>
			<?php echo nl2br($item->content) ?>
			<?php echo $item->is_recomend ? '<br/><br/><strong>Буду рекомендовать друзьям</strong><br/><br/>' : '' ?>
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
				<div class="answerright"><?php echo nl2br($comment->content) ?></div>
			</div>
			<?php endforeach ?>
		</div>

		<div class="clear"></div>

		<div class="sharebox">
			<a href="" class="sharelink">Поделиться</a>
			<div class="sharelist">

				<ul>
					<li><a href="javascript:void(0)" class="facebook">Facebook</a></li>
					<li><a href="javascript:void(0)" class="vkontakte">Вконтакте</a></li>
					<li><a href="javascript:void(0)" class="mailru">Mail.ru</a></li>
					<li><a href="javascript:void(0)" class="odnoklassniki">Одноклассники</a></li>
					<li><a href="javascript:void(0)" class="twitter">Twitter</a></li>
				</ul>
			</div>
		</div>

		<a href="javascript:void(0)" class="button whitelink subcomments-add-trigger">Написать комментарий</a>
		<div class="addcomment">
			<form method="post" action="<?php echo url_for('productComment_create', $sf_data->getRaw('product')) ?>">
				<div class="pb10">Текст сообщения</div>
				<div class="textareabox"><textarea rows="4" cols="125" name="content"></textarea></div>
				<input type="hidden" name="parent_id" value="<?php echo $item->id ?>"/>
				<input type="submit" class="button yellowbutton" value="Отправить" />
			</form>
		</div>
	</div>
</div>
<?php endforeach ?>
<!-- /Comments  -->


<div class="clear"></div>