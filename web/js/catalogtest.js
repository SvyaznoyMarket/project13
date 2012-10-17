goodsbox_hover = function(){
	
}
$(document).ready(function(){
	$('.goodsbox__inner.js').hover(
		//on
		function(){
			goodsbox.hoverOn(this);
		},
		//off
		function(){
			goodsbox.hoverOff(this);	
		}
	);
});

goodsbox = {
	timeoutId:0,
	shadowTimeoutId:0,
	hoverTrigger:false,
	shadowAnim:function(obj, start, stop, alpha){
		shadowTimeoutId = setTimeout( function(){
			if (start<stop){
				alpha+=0.1;
				//console.log(alpha);
			}
			else{
				alpha-=0.1;
			}
			if ((alpha>1)||(alpha<0.1)){
				//console.log('stop animaton '+alpha);
				$(obj).css('box-shadow','0 0 11px 7px rgba(230, 230, 230,'+stop+')');
				console.log('stop anim');
				//clearTimeout(shadowTimeoutId);
			}
			else{
				//console.log('continue animaton '+alpha);
				$(obj).css('box-shadow','0 0 11px 7px rgba(230, 230, 230,'+alpha+')');
				goodsbox.shadowAnim(obj, start, stop, alpha);
			}
		},15);			
	},
	hoverOn: function(box){
		timeoutId = setTimeout( function(){
			goodsbox.hoverTrigger = true;
			console.log('hover on');
			var img = $(box).find('.mainImg');
			var h = img.height();
			var w = img.width();
			img.stop(true,true).animate({'height':h+3,'width':w+3,'marginTop':'-3px'},150);
			if (window.navigator.userAgent.indexOf ("MSIE") >= 0){
				$(box).addClass('hover');
			}
			else{
				console.log('start anim');
				goodsbox.shadowAnim(box, 0, 1, 0);
			}
		} , 400)
	},
	hoverOff: function(box){
		clearTimeout(timeoutId);
		if (goodsbox.hoverTrigger){
			console.log('hover off');
			goodsbox.hoverTrigger = false;
			var img = $(box).find('.mainImg');
			var h = img.height();
			var w = img.width();
			img.stop(true,true).animate({'height':h-3,'width':w-3,'marginTop':'0'},150);
			if (window.navigator.userAgent.indexOf ("MSIE") >= 0){
				$(box).removeClass('hover');
			}
			else{
				console.log('start anim');
				clearTimeout(shadowTimeoutId);
				goodsbox.shadowAnim(box, 1, 0, 1);
			}
		}
	}
}
