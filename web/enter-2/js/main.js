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
});