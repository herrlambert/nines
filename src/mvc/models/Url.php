<?php
namespace nines\mvc\models;


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
}
