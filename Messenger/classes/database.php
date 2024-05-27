<?php

Class Database
{

    private $con;

    function _construct()
    {

        $this->con = $this->connect();
    }

    private function connect()
    {

        $string = "mysql:host=localhost;messenger_db";
        try
        {
            $connection = new PDO($string,DBUSER,DBPASS);
            return $connection;

        }catch(PDOException $e)
        {
            echo $e->getMessage();
            die;
        }
        

    }
}
