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
$urlsArray = $urlController->getUrlsByUrlGroupId(1);
$pinger = Pinger::getInstance($responseLogger);

// Ping URLs and log results
$pingResult = $pinger->pingUrls($urlsArray);
echo($pingResult);
