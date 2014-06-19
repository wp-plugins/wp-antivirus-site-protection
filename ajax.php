<?php

include_once(dirname(__FILE__).'/sgantivirus.class.php');

error_reporting(0);

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