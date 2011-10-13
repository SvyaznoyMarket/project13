$(document).ready(function(){

	$('.viewstock').bind( 'mouseover', function(){
		var trgtimg = $('#stock img[ref="'+$(this).attr('ref')+'"]')
		var isrc    = trgtimg.attr('src')
		if( trgtimg[0].complete ) 
			$('#goodsphoto img').attr('src', isrc)
	})
	//$.proxy( $('.viewstock'), 'mouseover')
	
	//var lkmv = null
	var api = {
		'makeLite' : 'turnlite',
		'makeFull' : 'turnfull',
		'loadbar'  : 'percents',
		'zoomer'   : 'bigpopup .scale',
		'rollindex': '.scrollbox div b',
		'propriate': ['versioncontrol','scrollbox']
	}
	var bigimages = [
	'360/bimages/2063_whi!_01.jpg', '360/bimages/2063_whi!_02.jpg', '360/bimages/2063_whi!_03.jpg', '360/bimages/2063_whi!_04.jpg', '360/bimages/2063_whi!_05.jpg', '360/bimages/2063_whi!_06.jpg', '360/bimages/2063_whi!_07.jpg', '360/bimages/2063_whi!_08.jpg', '360/bimages/2063_whi!_09.jpg', '360/bimages/2063_whi!_10.jpg',
	'360/bimages/2063_whi!_11.jpg', '360/bimages/2063_whi!_12.jpg', '360/bimages/2063_whi!_13.jpg', '360/bimages/2063_whi!_14.jpg', '360/bimages/2063_whi!_15.jpg', '360/bimages/2063_whi!_16.jpg', '360/bimages/2063_whi!_17.jpg', '360/bimages/2063_whi!_18.jpg', '360/bimages/2063_whi!_19.jpg', '360/bimages/2063_whi!_20.jpg',
	'360/bimages/2063_whi!_21.jpg', '360/bimages/2063_whi!_22.jpg', '360/bimages/2063_whi!_23.jpg', '360/bimages/2063_whi!_24.jpg', '360/bimages/2063_whi!_25.jpg', '360/bimages/2063_whi!_26.jpg', '360/bimages/2063_whi!_27.jpg', '360/bimages/2063_whi!_28.jpg', '360/bimages/2063_whi!_29.jpg', '360/bimages/2063_whi!_30.jpg',
	'360/bimages/2063_whi!_31.jpg', '360/bimages/2063_whi!_32.jpg', '360/bimages/2063_whi!_33.jpg', '360/bimages/2063_whi!_34.jpg', '360/bimages/2063_whi!_35.jpg', '360/bimages/2063_whi!_36.jpg', '360/bimages/2063_whi!_37.jpg', '360/bimages/2063_whi!_38.jpg', '360/bimages/2063_whi!_39.jpg', '360/bimages/2063_whi!_40.jpg'        	
	]
	var smallimages = [
	'360/images/2063_whi!_01.jpg', '360/images/2063_whi!_02.jpg', '360/images/2063_whi!_03.jpg', '360/images/2063_whi!_04.jpg', '360/images/2063_whi!_05.jpg', '360/images/2063_whi!_06.jpg', '360/images/2063_whi!_07.jpg', '360/images/2063_whi!_08.jpg', '360/images/2063_whi!_09.jpg', '360/images/2063_whi!_10.jpg',
	'360/images/2063_whi!_11.jpg', '360/images/2063_whi!_12.jpg', '360/images/2063_whi!_13.jpg', '360/images/2063_whi!_14.jpg', '360/images/2063_whi!_15.jpg', '360/images/2063_whi!_16.jpg', '360/images/2063_whi!_17.jpg', '360/images/2063_whi!_18.jpg', '360/images/2063_whi!_19.jpg', '360/images/2063_whi!_20.jpg',
	'360/images/2063_whi!_21.jpg', '360/images/2063_whi!_22.jpg', '360/images/2063_whi!_23.jpg', '360/images/2063_whi!_24.jpg', '360/images/2063_whi!_25.jpg', '360/images/2063_whi!_26.jpg', '360/images/2063_whi!_27.jpg', '360/images/2063_whi!_28.jpg', '360/images/2063_whi!_29.jpg', '360/images/2063_whi!_30.jpg',
	'360/images/2063_whi!_31.jpg', '360/images/2063_whi!_32.jpg', '360/images/2063_whi!_33.jpg', '360/images/2063_whi!_34.jpg', '360/images/2063_whi!_35.jpg', '360/images/2063_whi!_36.jpg', '360/images/2063_whi!_37.jpg', '360/images/2063_whi!_38.jpg', '360/images/2063_whi!_39.jpg', '360/images/2063_whi!_40.jpg'        	    	
	]
	
	lkmv = new likemovie('photobox', api, smallimages, bigimages )
	var mLib = new mediaLib( $('#bigpopup') )	

	$('.viewme').click( function(){
		if( mLib )
			mLib.show( $(this).attr('ref') , $(this).attr('href'))
		return false
	})
	
})	