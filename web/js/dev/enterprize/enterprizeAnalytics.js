/**
 * Аналитика на странице подтверждения email/телефона
 */
(function() {
	var
		enterprize = $('.jsEnterprizeData'),
		data = {},
		toKiss = {};
	// end of vars

	if ( !enterprize.length ) {
		return;
	}

	data = enterprize.data('value');

	// --- Kiss ---
	if (typeof _kmq !== undefined) {
		toKiss = {
			'[Ent_Req] Name': data.name,
			'[Ent_Req] Phone': data.mobile,
			'[Ent_Req] Email': data.email,
			'[Ent_Req] Token name': data.couponName/*'<Имя фишки>'*/,
			'[Ent_Req] Token number': data.enterprizeToken/*'<Номер фишки>'*/,
			'[Ent_Req] Date': data.date/*'<Текущая дата>'*/,
			'[Ent_Req] Time': data.time/*'<Текущее время>'*/,
			'[Ent_Req] enter_id': data.client_id/*'<идентификатор клиента в cookie сайта>'*/
		};

		_kmq.push(['record', 'Enterprize Token Request', toKiss]);
	}

	// --- GA ---
	if (typeof _kmq !== undefined) {
		ga('send', 'event', 'Enterprize Token Request', data.enterprizeToken, '<идентификатор клиента в cookie сайта>');
	}
})();