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

    // Prevent contructing or cloning
    private function __construct() {}
    private function __clone() {}

    // Return instance
    public static function getInstance(\Nines\Shared\Controllers\ResponseLog $responseLogger)
    {
        if (self::$instance == null) {
            self::$instance = new Pinger();
            self::$instance->responseLogger = $responseLogger;
        }
        return self::$instance;
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
     * Send HEAD and (on error) GET requests to URLs in given array
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

        if (count($urlArrays) > 0) {

            $responseArray = array();
            $getResArray = array();

            foreach($urlArrays as $urlArray) {
                $headResArray = $this->sendHeadRequest($urlArray['url']);
                $headResArray['url'] = $urlArray['url'];
                $headResArray['urlId'] = $urlArray['id'];

                if ($headResArray['statusCode'] >= 400 ) {
                    $getResArray = $this->sendGetRequest($urlArray['url']);
                    $getResArray['url'] = $urlArray['url'];
                    $getResArray['urlId'] = $urlArray['id'];
                }

                $responseArray['head_response'] = $headResArray;
                $responseArray['get_response'] = (!empty($getResArray)) ? $getResArray : null;

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
        if (count($requestResultsArray) > 0) {
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
                $headers[] = $response->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $response->getStatusCode();

            } catch (ClientException $exception) {
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();

            } catch (ServerException $exception) {
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = date("Y-m-d H:i:s", time());
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();

            } catch (GuzzleException $exception) {
                $responseArray['error'] = $exception->getMessage();
            }
        }

        return $responseArray;
    }

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
