<?php
namespace Nines\Shared\Models;

class ResponseLog
{
    private $dbConn;

    // Disallow cloning of this class
    private function __clone() {}

    public function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    /**
     * Add a HEAD request response to the database
     *
     * @param array $responseArray
     * @return bool
     */
    public function addHeadResponse(Array $responseArray)
    {
        $result = false;

        if (isset($responseArray['urlId'], $responseArray['statusCode'], $responseArray['datetime'])) {

            try {
                $query = "
                    INSERT INTO head_response_log (`id`,`url_id`,`request_time`,`status_code`)
                    VALUES (NULL, :urlId, :datetime, :statusCode)";

                $stmt = $this->dbConn->prepare($query);
                $stmt->bindParam(':urlId',      $responseArray['urlId'],      \PDO::PARAM_INT);
                $stmt->bindParam(':datetime',   $responseArray['datetime'],   \PDO::PARAM_STR);
                $stmt->bindParam(':statusCode', $responseArray['statusCode'], \PDO::PARAM_INT);

                $result = $stmt->execute();

            } catch( \Exception $e ) {
                // Catch generic Exceptions
                die(print_r($e));
            }
        }
        return $result;
    }

    /**
     * Add a GET request response to the database
     *
     * @param array $responseArray
     * @return bool
     */
    public function addGetResponse(Array $responseArray)
    {
        $result = false;

        if (isset($responseArray['urlId'], $responseArray['statusCode'], $responseArray['datetime'])) {

            try {
                $query = "
                    INSERT INTO get_response_log (`id`,`url_id`,`request_time`,`status_code`,`error_message`,
                    `response_body`)
                    VALUES (NULL, :urlId, :datetime, :statusCode, :errorMessage, :responseBody)";

                $stmt = $this->dbConn->prepare($query);
                $stmt->bindParam(':urlId',        $responseArray['urlId'],        \PDO::PARAM_INT);
                $stmt->bindParam(':datetime',     $responseArray['datetime'],     \PDO::PARAM_STR);
                $stmt->bindParam(':statusCode',   $responseArray['statusCode'],   \PDO::PARAM_INT);
                $stmt->bindParam(':errorMessage', $responseArray['errorMessage'], \PDO::PARAM_LOB);
                $stmt->bindParam(':responseBody', $responseArray['responseBody'], \PDO::PARAM_LOB);

                $result = $stmt->execute();

            } catch( \Exception $e ) {
                // Catch generic Exceptions
                die(print_r($e));
            }
        }
        return $result;
    }
}
