/**
 * Для страницы регистрации юредических лиц
 */
;(function( global ) {
	var companyData = $('.bCompanyData'),
		bCompanyDataLink = companyData.find('.bCompanyDataLink'),
		toggleRegBtn = bCompanyDataLink.find('.bCompanyDataLink__eText'),
		toggleRegSection = companyData.find('.bCompanyDataSection');
	// end of var

	/**
	 * Обработчик переключения состояния ввода реквизитов организации открыто или закрыто
	 */
	var companyRegToggle = function companyRegToggle() {
		bCompanyDataLink.toggleClass('mOpen');
		bCompanyDataLink.toggleClass('mClose');
		toggleRegSection.toggle();
	};

	toggleRegBtn.bind('click', companyRegToggle);
}(this));