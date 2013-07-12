/**
 * JIRA
 */
;(function(){
	$.ajax({
		url: "https://jira.enter.ru/s/en_US-istibo/773/3/1.2.4/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector.js?collectorId=2e17c5d6",
		type: "get",
		cache: true,
		dataType: "script"
	});
	
	window.ATL_JQ_PAGE_PROPS =  {
		"triggerFunction": function(showCollectorDialog) {
			$("#jira").click(function(e) {
				e.preventDefault();
				showCollectorDialog();
			});
		}
	};
}());