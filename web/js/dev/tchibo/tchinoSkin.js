/**
 * White floating user bar
 *
 * 
 * @requires jQuery, ENTER.utils, ENTER.config
 * @author	Zaytsev Alexandr
 *
 * @param	{Object}	ENTER	Enter namespace
 */
;(function( ENTER ) {
	var 
		listItem = $('.tchiboNav__list .jsItemListTchibo'),
		listItemActive = $('.tchiboNav__list .jsItemListTchibo.active'),
		listItemLink = listItem.find('.link'),
		subList = listItem.find('.tchiboNav__sublist');
	// end of vars

	listItemLink.each ( 
		function listItemWidth( i ) {

		var
			listItemWidth = listItem.eq(i).width();
		// end of vars

	  		listItemLink.eq(i).css({ 'width' : listItemWidth + 25});
		}
	);

	var listItemHover = function listItemHover() {

		if ( $(this).hasClass("active") || (!$(this).children(".tchiboNav__sublist").length ) ) {
		    listItemActive.find(subList).css({'opacity':'1'});
		}
		else {
			listItemActive.find(subList).css({'opacity':'0'});
		}
	};

	var listItemUnHover = function listItemUnHover() {

		listItemActive.find(subList).css({'opacity':'1'});
	};

	listItem.hover(listItemHover, listItemUnHover);

}(window.ENTER));
