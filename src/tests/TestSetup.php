<?php
/**
 * Setup Object Provides Database Connection and User objects for all tests
 */
namespace nines\tests;

use nines\lib\Database;

require_once('test-config.php');

class TestSetup
{
    // Store a single instance of this class
    private static $instance = null;

    // Store database connection and user objects
    private $db = null;
    private $dbConn = null;

    // Declare empty private methods to prevent users from trying to do anything
    // via these method calls
    private function __construct() {}
    private function __clone() {}

    // Method for returning instance
    public static function getInstance()
    {

        if (self::$instance == null) {
            // Create the new instance
            self::$instance = new TestSetup();

            // Create test database/connection and user objects
            self::$instance->db = Database::getInstance();
            self::$instance->db->setDbAllParams(TEST_DB_NAME, TEST_DB_HOST, TEST_DB_PORT, TEST_DB_USER, TEST_DB_PASS, 'utf8');
            self::$instance->db->createDbConnection();
            self::$instance->dbConn = self::$instance->db->getDbConnection();
        }

        return self::$instance;
    }

    // Provide database connection for tests
    public function getDbConn() {
        return $this->dbConn;
    }
}
