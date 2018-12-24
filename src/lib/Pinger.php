<?php
namespace Nines\Lib;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Pinger object with methods for pinging URLs and recording responses.
 *
 * Uses private static instance property to implement Singleton pattern. Only one instance of this class will
 * be available at a time for a process.
 *
 * Class Pinger
 * @package Nines\Lib
 */
class Pinger
{
    // Store a single instance of this class
    private static $instance = null;

    private $responseLogger = null;
    private $pingFrequencyValues = null;

    // Prevent constructing or cloning
    private function __construct() {}
    private function __clone() {}

    // Return instance
    public static function getInstance(\Nines\Shared\Controllers\ResponseLog $responseLogger)
    {
        if (self::$instance == null) {
            self::$instance = new Pinger();
            self::$instance->responseLogger = $responseLogger;

            self::$instance->pingFrequencyValues = array(
                'every five minutes' => 5,
                'every fifteen minutes' => 15,
                'every half-hour' => 30,
                'every hour' => 60,
                'twice daily' => 12,
                'once daily' => 24
            );
        }
        return self::$instance;
    }

    public function getUrlGroupsToPing(Array $urlGroups, Array $pingFrequencies, $currentHour, $currentMinute)
    {
        $currentMinute = 10;
        $currentHour = 3;
        if ($currentMinute % 5 === 0) {
            $frequencyKeys = array('5_min'); // TODO: Get this from function instead of hard-coding
            foreach($pingFrequencies as $pingFrequency) {
                if ($pingFrequency['hour_value'] === 0) {
                    if ($pingFrequency['minute_value'] % $currentMinute === 0) {
                        $frequencyKeys[] = $pingFrequency['key'];
                    }
                } else {
                    if (($pingFrequency['minute_value'] % $currentMinute === 0) &&
                        ($pingFrequency['hour_value'] % $currentHour === 0)) {
                        $frequencyKeys[] = $pingFrequency['key'];
                    }
                }
            }
            die(print_r($frequencyKeys));
        }
        die('nope');

        // Determine frequency values relevant for the give current hour and minute
        $frequencies = array();
        if ($currentMinute % $frequencyValues['every five minutes'] === 0) {
            $frequencies[] = $frequencyValues['every five minutes']; }

        if ($currentMinute % $frequencyValues['every fifteen minutes'] === 0) {
            $frequencies[] = $frequencyValues['every fifteen minutes'];
        }
        if ($currentMinute % $frequencyValues['every half-hour'] === 0) {
            $frequencies[] = $frequencyValues['every half-hour'];
        }
        if ($currentMinute % $frequencyValues['every hour'] === 0) {
            $frequencies[] = $frequencyValues['every hour'];
        }
        if (($currentHour % $frequencyValues['twice daily'] === 0) &&
            ($currentMinute % $frequencyValues['every hour'] === 0)) {
            $frequencies[] = $frequencyValues['twice daily'];
        }
        if (($currentHour % $frequencyValues['once daily'] === 0) &&
            ($currentMinute % $frequencyValues['every hour'] === 0)) {
            $frequencies[] = $frequencyValues['once daily'];
        }

        die(print_r($frequencies));



        return $frequencies;
    }

    /**
     * @param array $urlArrays
     * @return bool
     */
    public function pingUrls(Array $urlArrays = array())
    {
        $requestResults = $this->sendRequests($urlArrays);
        return $this->logRequestResults($this->responseLogger, $requestResults);
    }

    /**
     * Send HEAD and (on error) GET requests to URLs in given array and return results in format shown below
     *
     * Result array format:
     * array(
     *     [#] => array(
     *            [head_response] => array( [url], [urlId], [datetime], [statusCode] ),
     *            [get_response]  => array( [url], [urlId], [datetime], [statusCode], [errorMessage], [responseBody] )
     *      ),
     * )
     *
     * @param array $urlArrays
     * @return array
     */
    private function sendRequests(Array $urlArrays)
    {
        $requestResultArray = array();

        if (!empty($urlArrays)) {

            $responseArray = array();

            foreach($urlArrays as $urlArray) {
                $getResArray = array();
                $headResArray = $this->sendHeadRequest($urlArray['url']);
                $headResArray['url'] = $urlArray['url'];
                $headResArray['urlId'] = $urlArray['id'];

                if (isset($headResArray['statusCode']) && $headResArray['statusCode'] >= 400 ) {
                    $getResArray = $this->sendGetRequest($urlArray['url']);
                    $getResArray['url'] = $urlArray['url'];
                    $getResArray['urlId'] = $urlArray['id'];
                }

                $responseArray['head_response'] = $headResArray;
                $responseArray['get_response'] = $getResArray;

                $requestResultArray[] = $responseArray;
            }
        }
        return $requestResultArray;
    }

    /**
     * Log results from HEAD and GET requests provided in given array to database
     *
     * @param \Nines\Shared\Controllers\ResponseLog $responseLogger
     * @param array $requestResultsArray
     * @return bool
     */
    private function logRequestResults(
        \Nines\Shared\Controllers\ResponseLog $responseLogger ,
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

    /**
     * Send HEAD request for given URL and return response as an array
     *
     * @param string $url
     * @return array
     */
    private function sendHeadRequest($url)
    {
        if (is_string($url) && !empty($url)) {
            $client = new Client();
            $responseArray = [];
            try {
                $response = $client->request('HEAD', $url, ['verify' => false]);
                // < 400-level responses (not errors)
                $headers[] = $response->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $response->getStatusCode();

            } catch (ClientException $exception) {
                // 400-level responses
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();

            } catch (ServerException $exception) {
                // 500-level responses
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();

            } catch (GuzzleException $exception) {
                // No response, or request timed out
                $responseArray['error'] = $exception->getMessage();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = 0;
            }
        }
        return $responseArray;
    }

    /**
     * Send GET request for given URL and return response as an array
     *
     * @param $url
     * @return array
     */
    private function sendGetRequest($url)
    {
        if (is_string($url) && !empty($url)) {

            $client = new Client();
            $responseArray = array();

            try {
                $request = New Request('GET', $url);
                $response = $client->send($request, ['verify' => false]);
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $response->getStatusCode();

            } catch (ClientException $exception) {
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getCode();
                $responseArray['errorMessage'] = $exception->getMessage();
                $responseArray['responseBody'] = Psr7\str($exception->getResponse());

            } catch (ServerException $exception) {
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getCode();
                $responseArray['errorMessage'] = $exception->getMessage();
                $responseArray['responseBody'] = Psr7\str($exception->getResponse());

            } catch (GuzzleException $exception) {
                $responseArray['error'] = $exception->getMessage();
            }
        }

        return $responseArray;
    }
}
