<?php
namespace Nines\Shared\Controllers;

use Nines\Shared\Models;

class ResponseLog
{
    private $responseLogModel = null;

    // Disallow cloning of this class
    private function __clone() {}

    public function __construct($dbConn)
    {
        // Create instance of Model
        $this->responseLogModel = new models\ResponseLog($dbConn);
    }

    /**
     * Add a HEAD request response to the database
     *
     * @param array $responseArray
     * @return bool
     */
    public function addHeadResponse(Array $responseArray)
    {
        return $this->responseLogModel->addHeadResponse($responseArray);
    }

    /**
     * Add a GET request response to the database
     *
     * @param array $responseArray
     * @return bool
     */
    public function addGetResponse(Array $responseArray)
    {
        return $this->responseLogModel->addGetResponse($responseArray);
    }
}
