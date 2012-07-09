<?php
	$jsonAdr = array (
		'productId' => $adriverProductInfo['productId'],
		'categoryId' => $adriverProductInfo['categoryId']
	)
?>
<div id="adriverCommon" data-vars='<?php echo json_encode( $jsonAdr ) ?>' class="jsanalytics"></div>