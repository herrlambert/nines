<?php
namespace Nines\Lib;

/**
 * ResponseMetrics object with methods for tabulating response result metrics.
 *
 * Uses private static instance property to implement Singleton pattern. Only one instance of this class will
 * be available at a time for a process.
 *
 * Class ResponseMetrics
 * @package Nines\Lib
 */
class ResponseMetrics
{
    // Store a single instance of this class
    private static $instance = null;

    private $responseLogger = null;

    // Prevent constructing or cloning
    private function __construct() {}
    private function __clone() {}

    // Return instance
    public static function getInstance(
        \Nines\Shared\Controllers\ResponseLog $responseLogger ) {

        if (self::$instance == null) {
            self::$instance = new ResponseMetrics();
            self::$instance->responseLogger = $responseLogger;
        }
        return self::$instance;
    }

    public function tabMetricsForUrlGroup() {

    }

    /**
     * Log results from HEAD and GET requests provided in given array to database
     *
     * @param \Nines\Shared\Controllers\ResponseLog $responseLogger
     * @param array $requestResultsArray
     * @return bool
     */
    private function logRequestResults(
        \Nines\Shared\Controllers\ResponseLog $responseLogger,
        Array $requestResultsArray) {

        $result = false;
        if (!empty($requestResultsArray)) {
            foreach($requestResultsArray as $requestResultArray) {
                if (!empty($requestResultArray['head_response'])) {
                    $result = $responseLogger->addHeadResponse($requestResultArray['head_response']);
                }
                if (!empty($requestResultArray['get_response'])) {
                    $result = $responseLogger->addGetResponse($requestResultArray['get_response']);
                }
            }
            return $result;
        }
        return $result;
    }


}
