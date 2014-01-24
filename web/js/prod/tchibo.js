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
		listItem = $(".tchiboNav__list .jsItemListTchibo"),
		listItemLink = $(".tchiboNav__list .jsItemListTchibo .link");
	// end of vars

	listItemLink.each ( 
		function listItemWidth( i ) {

		var
			listItemWidth = listItem.eq(i).width();
		// end of vars

	  		listItemLink.eq(i).css({ 'width' : listItemWidth + 25});
		}
	);

}(window.ENTER));
