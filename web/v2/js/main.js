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
	
	var header = $('.header'),
		headerHeight = $('.header').height(),
		headerHeightInner = $('.header').innerHeight(),
		navIco = $('.navIco'),
		navSite = $('.nav'),
		navSiteHeight = navSite.height(),
		navSiteItemLevel1 = navSite.find('.navList__text'),
		navSiteListLevel2 = navSite.find('.navListLevel2');
	//var
	
		// header.css({'height' : headerHeight});
		// navSite.css({'top' : -navSite.height()-headerHeight, 'z-index': '1'});

	var allPanels = navSite.hide();
	var allPanels2 = navSiteListLevel2.hide();

	var
		slideNav = function slideNav() {
			//navSite.css({'top' : headerHeight+10});
			navSite.slideToggle();

			return false;
		},

		slideNavLevel2 = function slideNavLevel2() {
			navSiteListLevel2.slideUp();
			$(this).next(navSiteListLevel2).slideDown();

			return false;
		};

	navIco.click(slideNav);
	navSiteItemLevel1.click(slideNavLevel2);
});