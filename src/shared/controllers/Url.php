<?php
namespace Nines\Shared\Controllers;

use Nines\Shared\Models;

class Url
{
    private $urlModel = null;

    // Disallow cloning of this class
    private function __clone() {}

    public function __construct($dbConn)
    {
        // Create instance of Model
        $this->urlModel = new models\Url($dbConn);
    }

    /**
     * Return all columns for all rows of URL Groups in a multidimensional array format
     *
     * @return array
     */
    public function getUrlGroups()
    {
        return $this->urlModel->getUrlGroups();
    }

    /**
     * Return URL ids and URLs for URLs associated with given URL Group Id
     *
     * @param $urlGroupId
     * @return array
     */
    public function getUrlsByUrlGroupId($urlGroupId)
    {
        return $this->urlModel->getUrlsByUrlGroupId($urlGroupId);
    }

    /**
     * Return all URLGroup info for given URLGroup ID
     *
     * @param $urlGroupId
     * @return array
     */
    /*
     * REMOVE IF THIS DOES NOT GET USED
    public function getUrlGroupInfo($urlGroupId)
    {
        return $this->urlModel->getUrlGroupInfo($urlGroupId);
    }
     */

    /**
     * Return all columns for all rows of Ping Frequencies in a multidimensional array format
     *
     * @return array
     */
    public function getPingFrequencies()
    {
        return $this->urlModel->getPingFrequencies();
    }
}

