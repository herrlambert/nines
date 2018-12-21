<?php
require "../../../vendor/autoload.php";
require('../../../config/config.php');


use Nines\Lib\Database;
use Nines\Lib\Pinger;
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
$urlsArray1 = $urlController->getUrlsByUrlGroupId(1);
$urlsArray2 = $urlController->getUrlsByUrlGroupId(2);
$urlsArray3 = $urlController->getUrlsByUrlGroupId(3);
$urlsArray = array_merge($urlsArray1, $urlsArray2, $urlsArray3);
$pinger = Pinger::getInstance($responseLogger);

// Ping URLs and log results
$pingResult = $pinger->pingUrls($urlsArray);
echo ($pingResult > 0) ? "Success" : "Failure";
