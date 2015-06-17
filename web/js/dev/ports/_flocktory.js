ANALYTICS.flocktoryScriptJS = function() {
    $LAB.script('//api.flocktory.com/v2/loader.js?site_id=427');
};

ANALYTICS.flocktoryCompleteOrderJS = function() {
    var data = $('#flocktoryCompleteOrderJS').data('value');
    window.flocktory = window.flocktory || [];
    console.info('Flocktory data', data);
    window.flocktory.push(['postcheckout', data]);
};