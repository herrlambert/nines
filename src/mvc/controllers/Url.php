<?php
namespace nines\mvc\controllers;

use nines\mvc\models;


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

    public function getUrlGroups()
    {
        return $this->urlModel->getUrlGroups();
    }

    public function getUrlsByUrlGroupId($urlGroupId)
    {
        return $this->urlModel->getUrlsByUrlGroupId($urlGroupId);
    }
}

