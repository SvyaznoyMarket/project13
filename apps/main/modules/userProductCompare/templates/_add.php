<ul class="comparisonblock">
	<?php foreach ($productList as $product): ?>
	<li>
		<div class="photo"><b class="delete" title="Удалить"></b><a href="<?php echo url_for('productCard', $product) ?>">
			<img src="http://core.ent3.ru/upload/1/300/<?php echo $product['Photo'][0]['resource'] ?>" alt="" width="120" height="120" title="" />
		</a></div>
		<a href="<?php echo url_for('productCard', $product) ?>"><?php echo $product->name ?></a> <strong><?php echo $product->price ?> <span class="rubl">p</span></strong>
	</li>
	<?php endforeach ?>
	<li>
		<div class="comparphoto"></div>
		<div class="gray ac">Товар для сравнения</div>
	</li>
</ul>
<div class="fl form width230">
	<div class="pb5">Товары, которые вы сравнивали в других разделах:</div>
	<div class="selectbox selectbox225 mb70"><i></i>
	 <select class="styled" name="product_type_id">
		 <?php foreach ($productTypes as $productType): ?>
		 <option value="<?php echo $productType->id ?>"<?php echo $productType->id==$currType->id?' selected="selected"':'' ?>><?php echo $productType->name ?></option>
		 <?php endforeach ?>
	 </select>
	 </div>
	 <a href="<?php echo url_for('userProductCompare_show', $currType) ?>" class="button bigbuttonlink">Перейти в сравнение</a>
</div>