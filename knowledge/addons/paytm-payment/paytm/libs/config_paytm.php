<?php


define('PAYTM_ENVIRONMENT', 'TEST'); 
define('PAYTM_MERCHANT_KEY', 'xxxxxxxxxxxxxxxxxxxxxxxx'); 
define('PAYTM_MERCHANT_MID', 'xxxxxxxxxxxxxxxxxxxxxxx'); 
define('PAYTM_MERCHANT_WEBSITE', 'xxxxxxx'); 

$PAYTM_STATUS_QUERY_NEW_URL='https://securegw-stage.paytm.in/merchant-status/getTxnStatus';
$PAYTM_TXN_URL='https://securegw-stage.paytm.in/theia/processTransaction';
if (PAYTM_ENVIRONMENT == 'PROD') {
	$PAYTM_STATUS_QUERY_NEW_URL='https://securegw.paytm.in/merchant-status/getTxnStatus';
	$PAYTM_TXN_URL='https://securegw.paytm.in/theia/processTransaction';
}

define('PAYTM_REFUND_URL', '');
define('PAYTM_STATUS_QUERY_URL', $PAYTM_STATUS_QUERY_NEW_URL);
define('PAYTM_STATUS_QUERY_NEW_URL', $PAYTM_STATUS_QUERY_NEW_URL);
define('PAYTM_TXN_URL', $PAYTM_TXN_URL);
?>
