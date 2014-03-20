$(function(){

	var chooseModelWrap = $('.chooseModel'),
	    chooseModelMoreLink = chooseModelWrap.find('.chooseModel__moreLink'),
	    chooseModelMoreBox = chooseModelWrap.find('.chooseModel__moreBox'),
	    chooseModelMoreBoxDown = chooseModelWrap.find('.chooseModel__moreBox.more'),

		chooseModelMoreModel = function chooseModelMoreModel() {
			chooseModelMoreBox.slideToggle('800');
			chooseModelMoreLink.toggleClass('more');
		};
	// end of vars
		
	chooseModelMoreLink.click(chooseModelMoreModel);

	/**
	 * Навигация сайта, показывается при клике по иконке .navIco
	 */
	
	var navIco = $('.navIco'),
		navSite = $('.nav'),
		navSiteItemLevel1 = navSite.find('.navList__text'),
		navSiteListLevel2 = navSite.find('.navListLevel2');
	// end of vars

	navSite.hide();
	navSiteListLevel2.hide();

	var
		/**
		 * Показываем/скрываем навигацию
		 */
		slideNav = function slideNav() {
			navSite.slideToggle();
			navSiteListLevel2.slideUp();

			return false;
		},

		/**
		 * Показываем/скрываем навигацию второго уравня
		 */
		slideNavLevel2 = function slideNavLevel2() {
			navSiteListLevel2.slideUp();

			if ( ($(this).next(navSiteListLevel2)).is(':visible') ) {
				navSiteListLevel2.slideUp();
				return;
			}

			$(this).next(navSiteListLevel2).stop(true, false).slideDown();

			return false;
		},

		/**
		 * Скрываем навигацию при клике в любом месте кроме .nav
		 */
	    closeNav = function closeNav( e ) {
			if( $(e.target).closest(navSite).length ) 
			return;

			navSite.slideUp();
			navSiteListLevel2.slideUp();

			e.stopPropagation();
		};
	// end of vars
	
	navIco.click(slideNav);

	navSiteItemLevel1.click(slideNavLevel2);

	$(document).bind('click', closeNav);
});