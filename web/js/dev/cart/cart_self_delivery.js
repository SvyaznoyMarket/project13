;(function($){
	var cartInfoBlock = $('.cartInfo');

	$('.bGoodsSlider').goodsSlider();

	if (cartInfoBlock) ko.applyBindings(ENTER.UserModel, cartInfoBlock[0]);

}(jQuery));