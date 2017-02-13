$(function() {
	var
		isScriptLoaded = false,
		showCollectorDialog;

	$('.js-g-jira').click(function(e) {
		e.preventDefault();

		if (!isScriptLoaded) {
			isScriptLoaded = true;

			window.ATL_JQ_PAGE_PROPS = {
				'triggerFunction': function(func) {
					setTimeout(function() {
						showCollectorDialog = func;
						showCollectorDialog();
					}, 0);
				}
			};

			$LAB.script('https://jira.enter.ru/s/ru_RU-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6');
		} else if (showCollectorDialog) {
			showCollectorDialog();
		}
	});
});