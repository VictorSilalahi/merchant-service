<?php

class PgSql {

    public $db;

   
    function create($dbname) {

            $dsn = $_ENV["STAG_DB_DRIVER"].":host=".$_ENV["STAG_DB_HOST"].";port=".$_ENV["STAG_DB_PORT"].";dbname=".$dbname.";user=".$_ENV["STAG_DB_USERNAME"].";password=".$_ENV["STAG_DB_PASSWORD"];
    
            try{
                // create a PostgreSQL database connection
                $this->db = new \PDO($dsn);
                
                // display a message if connected to the PostgreSQL successfully
                if($this->db){
                    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    return $this->db;
                }
            } catch (PDOException $e){
                // report error message
                print_r($e->getMessage());
            } 
    }


}
