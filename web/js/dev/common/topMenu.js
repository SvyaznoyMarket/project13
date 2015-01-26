;$(function($){

	var $menu = $('.js-mainmenu-level2');

	$menu.menuAim({
		activate: activateSubmenu,
		deactivate: deactivateSubmenu,
		exitOnMouseOut: true
	});

	function activateSubmenu(row) {
		var $row = $(row),
	      $submenu = $row.children('ul');

		$row.addClass('hover');
		$submenu.css({display: 'block'});
	}

	function deactivateSubmenu(row) {
		var $row = $(row),
			$submenu = $row.children('ul');

		$row.removeClass('hover');
		$submenu.css('display', 'none');
	}

}(jQuery));