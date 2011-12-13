$(document).ready(function(){

  var regionlink = $('.regionselect .regionlink:first');
	var regionlist = $('.regionselect .regionlist');
	var userag    = navigator.userAgent.toLowerCase()
	var isAndroid = userag.indexOf("android") > -1
	var isOSX     = ( userag.indexOf('ipad') > -1 ||  userag.indexOf('iphone') > -1 )
	if( isAndroid || isOSX ) {
		regionlink.click(function(){
			regionlink.hide();
			regionlist.show();
			return false
		});
	} else {
		regionlink.mouseenter(function(){
			regionlink.hide();
			regionlist.show();
		});
		regionlist.mouseleave(function(){
			regionlist.hide();
			regionlink.show();
		});
	}

	$('.regionchoice a').click( function() {
		var button = this
    var form = $('form#region')
    form.attr('action', button.href)
    form.submit()

		return false
	})

})