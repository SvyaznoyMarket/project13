/**
 * Для страницы тыры пыры
 */
;(function() {
	var companyData = $('.bCompanyData'),
		bCompanyDataLink = companyData.find('.bCompanyDataLink'),
		toggleBtn = bCompanyDataLink.find('.bCompanyDataLink__eText'),
		toggleSection = companyData.find('.bCompanyDataSection')
		// end of var

			/**
			 * Обработчик переключения состояния листа магазинов открыто или закрыто
			 */
			var companyRegToggle = function shopToggle() {
				bCompanyDataLink.toggleClass('mOpen');
				bCompanyDataLink.toggleClass('mClose');
				toggleSection.toggle();
			};

			toggleBtn.bind('click', companyRegToggle);
}(this));