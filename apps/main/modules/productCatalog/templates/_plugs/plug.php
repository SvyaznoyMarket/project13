<!-- ________________________AdFox Asynchronous code START__________________________ -->
<!--enter-->
<!--Площадка: Enter.ru / * / *-->
<!--Тип баннера: 683х-->
<!--Расположение: <верх страницы>-->
<script type="text/javascript">
<!--
if (typeof(pr) == 'undefined') { var pr = Math.floor(Math.random() * 1000000); }
if (typeof(document.referrer) != 'undefined') {
  if (typeof(afReferrer) == 'undefined') {
    afReferrer = escape(document.referrer);
  }
} else {
  afReferrer = '';
}
var addate = new Date();
var dl = escape(document.location);
var pr1 = Math.floor(Math.random() * 1000000);

document.write('<div id="AdFox_banner_'+pr1+'"><\/div>');
document.write('<div style="visibility:hidden; position:absolute;"><iframe id="AdFox_iframe_'+pr1+'" width=1 height=1 marginwidth=0 marginheight=0 scrolling=no frameborder=0><\/iframe><\/div>');

AdFox_getCodeScript(1,pr1,'http://ads.adfox.ru/171829/prepareCode?pp=g&amp;ps=vto&amp;p2=emue&amp;pct=a&amp;plp=a&amp;pli=a&amp;pop=a&amp;pr=' + pr +'&amp;pt=b&amp;pd=' + addate.getDate() + '&amp;pw=' + addate.getDay() + '&amp;pv=' + addate.getHours() + '&amp;prr=' + afReferrer + '&amp;dl='+dl+'&amp;pr1='+pr1);
// -->
</script>
<!-- _________________________AdFox Asynchronous code END___________________________ -->
<?php if (false): ?>
      <!--  info block -->
			<div class='bInfoBlock'>
				<div id="rotator">
					<div>
						<img class="banner" src="/images/promo_1.jpg" alt=""/>
					</div>
					<div>
						<img class="banner" src="/images/promo_2.jpg" alt=""/>
					</div>
					<div>
						<img class="banner" src="/images/promo_3.jpg" alt=""/>
					</div>
				</div>
			</div>
			<!-- /info block -->
<?php endif ?>
