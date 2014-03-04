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

	// var navIco = $('.navIco'),
	//     navSite = $('.nav'),
	//     navSiteItemLevel1 = navSite.find('.navList__item');
	//     navSiteListLevel2 = navSiteItemLevel1.find('.navListLevel2');
	// //var
	
	// var slideNav = function slideNav() {
	// 	navSite.toggleClass('nav-drop');
	// 	navSite.slideToggle('200');
	// },
	//  slideNavLevel2 = function slideNavLevel2() {
	// 	navSiteListLevel2.slideToggle('200');
	// };

	// navIco.click(slideNav);
	// navSiteItemLevel1.click(slideNavLevel2);

	//верхнее меню
	
	var header = $('.header'),
		headerHeight = $('.header').height(),
		headerHeightInner = $('.header').innerHeight(),
		navIco = $('.navIco'),
		navSite = $('.nav'),
		navSiteHeight = navSite.height(),
		navSiteItemLevel1 = navSite.find('.navList__item'),
		navSiteListLevel2 = navSiteItemLevel1.find('.navListLevel2');
	//var
	
		header.css({'height' : headerHeight});
		navSite.css({'top' : headerHeight - 10});

	var
		slideNav = function slideNav() {
			navSite.slideToggle();
		},

		slideNavLevel2 = function slideNavLevel2() {

	    	

	    	$(this).children(navSiteListLevel2).show();
	    	navSite.css({'height' : navSiteHeight + navSiteItemLevel1.children(navSiteListLevel2).height()});
		
		};

	navIco.click(slideNav);
	navSiteItemLevel1.click(slideNavLevel2);
});