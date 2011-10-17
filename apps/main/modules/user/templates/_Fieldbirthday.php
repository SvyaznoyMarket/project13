<div class="pb10">
      <?php echo $form['birthday']->renderLabel() ?>:
      <?php echo $form['birthday']->renderError() ?>     
</div>
<?php $form['birthday']->getWidget()->setOption('format', '
	<div class="selectbox selectbox75 fl mr10"><i></i>%day%</div>
	<div class="selectbox selectbox98 fl mr10"><i></i>%month%</div>
	<div class="selectbox selectbox75 fl"><i></i>%year%</div>
') ?>

<?php echo $form['birthday']->render() ?>

<!--<div class="selectbox selectbox75 fl mr10"><i></i>
<?php //echo $form['birthday']->render(array('class' => 'styled' )) ?>
	<?php //echo $form['birthday']->renderRow() ?>
</div>-->

<div class="clear pb15"></div>