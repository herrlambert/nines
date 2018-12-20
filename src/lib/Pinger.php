<?php
namespace nines\lib;

use GuzzleHttp\Client;
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
 * @package ninespinger\lib
 */
class Pinger
{
    // Store a single instance of this class
    private static $instance = null;

    // Declare empty private methods to prevent users from trying to do anything
    // via these method calls
    private function __construct() {}
    private function __clone() {}

    // Method for returning instance
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Pinger();
        }
        return self::$instance;
    }

    /**
     * Ping Urls in given array of URLs and return results in an array
     *
     * @param array $urlArrays
     * @return array
     */
    public function pingUrls(Array $urlArrays = [])
    {
        $requestResultArray = [];

        if (count($urlArrays) > 0) {

            foreach($urlArrays as $urlArray) {
                $responseArray = $this->sendHeadRequest($urlArray['url']);
                $responseArray['url'] = $urlArray['url'];
                $responseArray['url_id'] = $urlArray['id'];
                $requestResultArray[] = $responseArray;
            }
        }

        return $requestResultArray;
    }

    /**
     * Send HEAD request for given URL and return response as an array
     *
     * @param string $url
     * @return array
     */
    private function sendHeadRequest($url = '')
    {
        if (is_string($url) && !empty($url)) {

            $client = new Client();
            $responseArray = [];
            $responseArray['url'] = $url;

            try {
                $response = $client->request('HEAD', $url, ['verify' => false]);
                $headers[] = $response->getHeaders();
                $responseArray['datetime'] = $headers[0]['Date'][0];
                $responseArray['statusCode'] = $response->getStatusCode();
            } catch (ClientException $exception) {
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = $headers[0]['Date'][0];
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();
            } catch (ServerException $exception) {
                $headers[] = $exception->getResponse()->getHeaders();
                $responseArray['datetime'] = $headers[0]['Date'][0];
                $responseArray['statusCode'] = $exception->getResponse()->getStatusCode();
            } catch (GuzzleException $exception) {
                $responseArray['error'] = $exception->getMessage();
            }
        }

        return $responseArray;
    }
}
