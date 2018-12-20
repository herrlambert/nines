<?php
namespace nines\lib;

/**
 * Class Database
 *
 * Uses the singleton pattern to provide a database connection using PDO
 *
 * @package oris\nines-pinger
 */
class Database
{
    // Store a single instance of this class
    private static $instance = NULL;

    // Array for storing database connection parameters
    private $dbConnectionParms = array();

    // Store database connection
    private $connection = NULL;

    // Declare empty private methods to prevent users from trying to do anything
    // via these method calls
    private function __construct() {}
    private function __clone() {}

    // Method for returning instance
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Set database connection parameter: name
    public function setDbName($dbName)
    {
        $this->dbConnectionParms['dbName'] = $dbName;
    }

    // Set database connection parameter: host
    public function setDbHost($dbHost)
    {
        $this->dbConnectionParms['dbHost'] = $dbHost;
    }

    // Set the database connection parameter: port
    public function setDbPort($dbPort)
    {
        $this->dbConnectionParms['dbPort'] = $dbPort;
    }

    // Set database connection parameter: user
    public function setDbUser($dbUser)
    {
        $this->dbConnectionParms['dbUser'] = $dbUser;
    }

    // Set database connection parameter: user password
    public function setDbPass($dbPass)
    {
        $this->dbConnectionParms['dbPass'] = $dbPass;
    }

    // Set database connection parameter: character set
    public function setDbCharSet($dbCharSet)
    {
        $this->dbConnectionParms['dbCharSet'] = $dbCharSet;
    }

    // Set database connection parameters: all
    public function setDbAllParams($dbName, $dbHost, $dbPort, $dbUser, $dbPass, $dbCharSet) {
        if (isset($dbName)) $this->setDbName($dbName);
        if (isset($dbHost)) $this->setDbHost($dbHost);
        if (isset($dbPort)) $this->setDbPort($dbPort);
        if (isset($dbUser)) $this->setDbUser($dbUser);
        if (isset($dbPass)) $this->setDbPass($dbPass);
        if (isset($dbCharSet)) $this->setDbCharSet($dbCharSet);
    }

    // Get database all connection parameters (returns array)
    public function getDbAllParams()
    {
        return $this->dbConnectionParms;
    }

    // Method to create a database connection
    public function createDbConnection()
    {
        // Create database connection using PDO
        try {
            // Add the database name and host to connection parameters
            $dbNamePortHost =  "mysql:dbname={$this->dbConnectionParms['dbName']};";
            $dbNamePortHost .= "host={$this->dbConnectionParms['dbHost']};";

            // If Port and Character Set have been specified, then add them too
            if (isset($this->dbConnectionParms['dbPort'])) {
                $dbNamePortHost .= "port={$this->dbConnectionParms['dbPort']};";
            }
            if (isset($this->dbConnectionParms['dbCharSet'])) {
                $dbNamePortHost .= "charset=utf8";
            }

            // Create the PDO connection object
            $this->connection = new \PDO(
                $dbNamePortHost,
                $this->dbConnectionParms['dbUser'],
                $this->dbConnectionParms['dbPass']);

        } catch(PDOException $e) {

            // Report database connection error
            exit();

        }
    }

    // Method to retrieve database connection
    public function getDbConnection()
    {
        // Return the connection if it has been created
        if (isset($this->connection)) {

            return $this->connection;

        // Attempt to create the connection and return it if connection has not yet been created
        } else {

            $this->createDbConnection();

            if (isset($this->connection)) {
                return $this->connection;
            } else {
                return NULL;
            }

        }
    }
}
