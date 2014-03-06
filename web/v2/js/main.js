$(function(){

	var chooseModelWrap = $('.chooseModel'),
	    chooseModelMoreLink = chooseModelWrap.find('.chooseModel__moreLink'),
	    chooseModelMoreBox = chooseModelWrap.find('.chooseModel__moreBox'),
	    chooseModelMoreBoxDown = chooseModelWrap.find('.chooseModel__moreBox.more'),

		chooseModelMoreModel = function chooseModelMoreModel() {
			chooseModelMoreBox.slideToggle('800');
			chooseModelMoreLink.toggleClass('more');
		};
	//var
		
	chooseModelMoreLink.click(chooseModelMoreModel);

	//верхнее меню
	
	var navIco = $('.navIco'),
		navSite = $('.nav'),
		navSiteItemLevel1 = navSite.find('.navList__text'),
		navSiteListLevel2 = navSite.find('.navListLevel2');
	//var

	var allPanels = navSite.hide(),
		allPanels2 = navSiteListLevel2.hide(),

		slideNav = function slideNav() {

			navSite.slideToggle();

			navSiteListLevel2.slideUp();

			return false;
		},

		slideNavLevel2 = function slideNavLevel2() {

			navSiteListLevel2.slideUp();

			if ( ($(this).next(navSiteListLevel2)).is(':visible') ) {
				navSiteListLevel2.slideUp();
			}

			else $(this).next(navSiteListLevel2).slideDown();

			return false;
		};

	navIco.click(slideNav);

	navSiteItemLevel1.click(slideNavLevel2);
});