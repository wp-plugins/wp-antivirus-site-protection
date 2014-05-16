<?php

include_once(__DIR__.'/tmp/module.php');

$action = $_REQUEST['action'];

switch ($action)
{
	case 'StartScan_AJAX':
		StartScan_AJAX();
		break;
		
	case 'GetScanProgress_AJAX':
		GetScanProgress_AJAX();
		break;
}

exit;


// Start Scan AJAX
function StartScan_AJAX()
{
	SGAntiVirus_module::scan();	
}

// Get Scan Progress AJAX
function GetScanProgress_AJAX()
{
	echo SGAntiVirus_module::readProgress();
}


?>