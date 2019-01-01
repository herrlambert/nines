<?php
require "../../../vendor/autoload.php";
require('../../../config/config.php');


use Nines\Lib\Database;
use Nines\Lib\Pinger;
use Nines\Lib\ResponseMetrics;
use Nines\Shared\Controllers;

/**
 * Create a database connection
 */
$database = Database::getInstance();
$database->setDbAllParams(DB_NAME, DB_HOST, DB_PORT, DB_USER, DB_PASS, 'utf8');
$database->createDbConnection();
$dbConn = $database->getDbConnection();

/**
 * Main program execution - ping urls and output results
 */

// Create objects
$urlController = new Controllers\Url($dbConn);
$responseLogger = new Controllers\ResponseLog($dbConn);
$pinger = Pinger::getInstance($responseLogger);

// Get all URL Groups
$urlGroups = $urlController->getUrlGroups();

// Filter to get only groups to ping this time
$currentDate = getdate();
$allPingFrequencies = $urlController->getPingFrequencies();

$relevantPingFrequencyIds = $pinger->getRelevantPingFrequencyIds($allPingFrequencies, $currentDate['minutes'], $currentDate['hours']);

die(print_r($relevantPingFrequencyIds));

//$urlGroupsToPing = $pinger->getUrlGroupsToPing($urlGroups, $pingFrequencies,
//    $currentDate['hours'], $currentDate['minutes']);

$urlsArray1 = $urlController->getUrlsByUrlGroupId(1);
$urlsArray2 = $urlController->getUrlsByUrlGroupId(2);
$urlsArray3 = $urlController->getUrlsByUrlGroupId(3);
$urlsArray = array_merge($urlsArray1, $urlsArray2, $urlsArray3);

$responseMetrics = ResponseMetrics::getInstance($responseLogger);



//die(print_r(PING_FREQUENCY_VALUES['once daily']));
//die(print_r(getdate()['minutes']));


// Ping URLs and log results
//$pingResult = $pinger->pingUrls($urlsArray);
//if ($pingResult < 1) {
//    echo "Failure\n";
//}

$responseMetrics->tabMetricsForUrlGroup($urlController, 1);
