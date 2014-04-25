/**
 * Окно смены региона
 *
 */
$(function() {

		var body = $('body'),
			tabWrap = $('.js-tab-wrap'),
			tabList = tabWrap.find('.js-tab'),
			tab = tabList.find('.js-cont');
		// end of vars	

	
		function tabsToggle() {
			console.log('tabsToggle');

			var tabWrapWidth = tabWrap.width(),
				tabCount = tab.length;

			tab.css({'width': tabWrapWidth});	
			tabList.css({'width' : tabWrapWidth * tabCount});

			console.log(tabWrapWidth);
		};
		//end of function

	$('body').on('load resize', tabsToggle);

});

		
    