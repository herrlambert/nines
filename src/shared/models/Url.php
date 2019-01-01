<?php
namespace Nines\Shared\Models;


class Url
{
    private $dbConn;

    // Disallow cloning of this class
    private function __clone() {}

    public function __construct($dbConn)
    {
        $this->dbConn = $dbConn;
    }

    /**
     * Return all columns for all rows of URL Groups in a multidimensional array format
     *
     * @return array
     */
    public function getUrlGroups()
    {
        $urlGroups = array();

        try {

            $query = "
            SELECT ug.id, ug.name, ug.ping_frequency, ug.last_ping_time, ug.metrics_from_date, ug.metrics_to_date,
                   ug.current_total_requests, ug.current_total_errors, ug.availability_rating
            FROM urlgroups ug";

            $stmt = $this->dbConn->prepare($query);
            $result = $stmt->execute();
            if ( $result ) {
                $stmt->setFetchMode( \PDO::FETCH_ASSOC );
                $urlGroups = $stmt->fetchAll();
            }

        } catch(Exception $e) {

            // Catch generic Exceptions
            print_r($e);
        }

        return $urlGroups;
    }

    /**
     * Return URL ids and URLs for URLs associated with given URL Group Id
     *
     * @param $urlGroupId
     * @return array
     */
    public function getUrlsByUrlGroupId($urlGroupId)
    {
        $urls = array();

        try {

            $query = "
            SELECT u.id, u.url
            FROM urls u
            JOIN urlgroups ug
            ON ug.id = u.urlgroup_id
            WHERE ug.id = :urlGroupId";

            $stmt = $this->dbConn->prepare($query);
            $stmt->bindParam(':urlGroupId', $urlGroupId, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if ( $result ) {
                $stmt->setFetchMode( \PDO::FETCH_ASSOC );
                $urls = $stmt->fetchAll();
            }

        } catch(Exception $e) {

            // Catch generic Exceptions
            print_r($e);
        }

        return $urls;
    }

    /**
     * Return all URLGroup info for given URLGroup ID
     *
     * @param $urlGroupId
     * @return array
     */
    /*
     * REMOVE IF THIS DOES NOT GET USED
     *
    public function getUrlGroupInfo($urlGroupId)
    {
        $urlGroupInfo = array();

        try {

            $query = "
            SELECT name, ping_frequency, last_ping_time, metrics_from_date, metrics_to_date, current_total_requests,
                   current_total_errors, availability_rating
            FROM urlgroups
            WHERE id = :urlGroupId";

            $stmt = $this->dbConn->prepare($query);
            $stmt->bindParam(':urlGroupId', $urlGroupId, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if ( $result ) {
                $stmt->setFetchMode( \PDO::FETCH_ASSOC );
                $urlGroupInfo = $stmt->fetch();
            }

        } catch(Exception $e) {

            // Catch generic Exceptions
            print_r($e);
        }

        return $urlGroupInfo;
    }
    */

    /**
     * Return all columns for all rows of Ping Frequencies in a multidimensional array format
     *
     * @return array
     */
    public function getPingFrequencies()
    {
        $pingFrequencies = array();

        try {

            $query = "
            SELECT pf.id, pf.key, pf.name, pf.minute_value, pf.hour_value
            FROM ping_frequencies pf";

            $stmt = $this->dbConn->prepare($query);
            //die(print_r($stmt));

            $result = $stmt->execute();
            if ( $result ) {
                $stmt->setFetchMode( \PDO::FETCH_ASSOC );
                $pingFrequencies = $stmt->fetchAll();
            }

        } catch(Exception $e) {

            // Catch generic Exceptions
            print_r($e);
        }

        return $pingFrequencies;
    }
}
