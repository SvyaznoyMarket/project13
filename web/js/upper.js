$(document).ready(function(){
	var upper = $('#upper');
	var trigger = false;//сработало ли появление языка
	$(window).scroll(function(){
		if (($(window).scrollTop() > 600)&&(!trigger)){
			//появление языка
			trigger = true;
			upper.animate({'marginTop':'0'},400);
		}
		else if (($(window).scrollTop() < 600)&&(trigger)){
			//исчезновение
			trigger = false;
			upper.animate({'marginTop':'-30px'},400);
		}
	});
	upper.bind('click',function(){
		$(window).scrollTo('0px',400);
		return false;
	});
});