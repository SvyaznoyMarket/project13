<!-- Response -->
<h2 class="bold">Мой отзыв о <?php echo $item->name ?></h2>
<div class="line pb15"></div>

<form class="fl" method="post" id="rating_form" action="<?php echo url_for('productComment_new', $sf_data->getRaw('product')) ?>">


<div class="fl width450">
	<div class="font16 pb15">Моя общая оценка<span class="gray">*</span>:</div>
	<div class="ratingbig fl">
		<div class="ratingbox">
			<div class="current" style="width: 0px;"></div>
			<div><a class="ra1" title="Плохо" href="javascript:void(0)">Плохо</a></div>
			<div><a class="ra2" title="Удолетворительно" href="javascript:void(0)">Удолетворительно</a></div>
			<div><a class="ra3" title="Нормально" href="javascript:void(0)">Нормально</a></div>
			<div><a class="ra4" title="Хорошо" href="javascript:void(0)">Хорошо</a></div>
			<div><a class="ra5" title="Отлично" href="javascript:void(0)">Отлично</a></div>
		</div>
	</div>
	<div class="ratingresult" id="ratingresult"></div>
	<input name="rating" class="ratingvalue" type="hidden" value="<?php echo $sf_request->getParameter('rating', 0) ?>"/>
</div>

<?php if (false): ?>
<div class="fr width450">
	<div class="font16 pb15">Моя потребительская оценка:</div>
	<table class="gradetable mr20">
	<tbody>
		<?php foreach ($ratingTypes as $ratingType): ?>
		<tr>
			<th><?php echo $ratingType['name'] ?></th>
			<td>
			   <div class="ratingscale">
					<div class="ratingscalebox">
						<div class="current" style="width: 0px;"></div>
						<div><a class="ra1" title="Плохо" href="javascript:void(0)">Плохо</a></div>
						<div><a class="ra2" title="Удолетворительно" href="javascript:void(0)">Удолетворительно</a></div>
						<div><a class="ra3" title="Нормально" href="javascript:void(0)">Нормально</a></div>
						<div><a class="ra4" title="Хорошо" href="javascript:void(0)">Хорошо</a></div>
						<div><a class="ra5" title="Отлично" href="javascript:void(0)">Отлично</a></div>
					</div>
				</div>
				<div class="ratingscaleresult"></div>
				<input name="rating_type_<?php echo $ratingType['id'] ?>" class="ratingvalue" type="hidden" value="<?php echo $sf_request->getParameter('rating_type_'.$ratingType['id'], 0) ?>"/>
				<input name="rating_type[<?php echo $ratingType['id'] ?>]" type="hidden" value="0"/>
			</td>
		</tr>
		<?php endforeach ?>
	</tbody>
	</table>
</div>
<?php endif ?>
<!-- /Response  -->

<div class="clear"></div>


<!-- Add comment -->
<div class="fl width450 form">
		 <label for="" class="db font16 pb10">Достоинства:</label>
		 <div class="textareabox mb15"><textarea name="content_plus" rows="4" style="width:440px;"><?php echo $sf_request->getParameter('content_plus') ?></textarea></div>
		 <label for="" class="db font16 pb10">Недостатки:</label>
		 <div class="textareabox mb15"><textarea name="content_minus" rows="4" style="width:440px;"><?php echo $sf_request->getParameter('content_minus') ?></textarea></div>
		 <label for="" class="db font16 pb10">Резюме<span class="gray">*</span>:</label>
		 <div class="textareabox mb15"><textarea name="content_resume" rows="4" style="width:440px;"><?php echo $sf_request->getParameter('content_resume') ?></textarea></div>

		 <div class="font16 pb10">Вы бы хотели рекомендовать друзьям покупать этот товар?</div>
		 <ul class="pl20">
			 <li class="fl"><label for="radio-1">Да, конечно</label><input id="radio-1" name="is_recomend" type="radio" value="1" /></li>
			 <li class="fl ml30"><label for="radio-2">Нет, не буду!</label><input id="radio-2" name="is_recomend" type="radio" value="0" /></li>
		 </ul>

		 <div class="clear pb20"></div>

		 <div class="font16 pb10">Уведомить меня по e-mail или sms</div>
		 <ul class="checkboxlist pb10">
			 <li><label for="checkbox-1">Прислать сообщение о публикации моего отзыва</label><input id="checkbox-1" name="subscribe_publication" type="checkbox" value="1" /></li>
			 <li><label for="checkbox-2">Присылать сообщения, когда кто-нибудь прокомментирует мой отзыв</label><input id="checkbox-2" name="subscribe_comments" type="checkbox" value="1" /></li>
		 </ul>

		 <div class="selectbox selectbox225 mb15"><i></i>
			 <select class="styled" name="subscribe_delivery_type">
				 <option value="1">Куда доставлять сообщения?*</option>
				 <option value="2">По почте</option>
				 <option value="3">Через СМС</option>
			 </select>
		  </div>

		 <div class="font11 gray pb10">Внимание!<br />Вы всегда сможете отписаться от данной рассылки в самой рассылке или в личном кабинете<br />* поля обязательные для заполнения</div>
		 <input type="submit" class="button yellowbutton" value="Добавить отзыв" />
</div>
<!-- /Add comment -->
</form>

<!-- Rules -->
<div class="fr width450">
	<div class="font16 pb10">Правила написания отзывов:</div>
	<div class="gray">
		Ваше мнение очень ценно и для нас, и для других посетителей сайта.
		Расскажите, чем вам понравилось или не понравилось устройство, какие
		функции удобны в использовании, а какие нет, оправдались ли ваши ожидания
		и готовы ли вы порекомендовать это устройство друзьям.<br /><br />
		Мы не публикуем отзывы, написанные:<br /><br />
		<ul class="graylist">
			<li>на основе чужих мнений об устройстве,</li>
			<li>о том, что не относится к потребительским свойствам устройства,</li>
			<li>голословно, без аргументов и доказательств,</li>
			<li>с использованием ненормативной лексики и оскорблений,</li>
			<li> прописными буквами, а также с большим количеством орфографических ошибок.</li>
		</ul><br />
		Мы с радостью публикуем отзывы, написанные:<br /><br />
		<ul class="graylist">
			<li>реальным пользователем устройства,</li>
			<li>предметно, с указанием достоинств и недостатков,</li>
			<li>обоснованно, с приведением фактов и аргументов,</li>
			<li>вежливо и корректно,</li>
			<li>с соблюдением правил русского языка и правил, принятых в Интернете.</li>
		</ul>
	</div>
</div>
<!-- /Rules -->
<div class="clear"></div>