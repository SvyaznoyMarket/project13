/**
 * Блокер экрана
 *
 * @param	{Object}		noti		Объект jQuery блокера экрана
 * @param	{Function}		block		Функция блокировки экрана. На вход принимает текст который нужно отобразить в окошке блокера
 * @param	{Function}		unblock		Функция разблокировки экрана. Объект окна блокера удаляется.
 */
window.blockScreen = {
	noti: null,
	block: function( text ) {
		var self = this;

		console.warn('block screen');

		if ( self.noti ) {
			self.unblock();
		}

		self.noti = $('<div>').addClass('noti').html('<div><img src="/images/ajaxnoti.gif" /></br></br> '+ text +'</div>');
		self.noti.appendTo('body');

		self.noti.lightbox_me({
			centered:true,
			closeClick:false,
			closeEsc:false,
			onClose: function() {
				self.noti.remove();
			}
		});
	},

	unblock: function() {
		console.warn('unblock screen');

		this.noti.trigger('close');
	}
};