<?php

/**
 * MySQL Connector Wrapper Class
 * It is a very thin client, so if you want to check about a response, head to PHP's manual
 *
 * @author ahughes
 */
require_once (__DIR__ . '/../config.php');

//the global mysql connector variable
global $mysql;
$mysql = new MySQL();

class MySQL {

    public $mysqli;

    public function MySQL() {
        //creating the connection
        $this->mysqli = new mysqli(Config::mysql_host, Config::mysql_user, Config::mysql_pass, Config::mysql_schema);

        //taking care of the encoding to avoid nasty surprises
        $this->mysqli->set_charset("utf8");
    }

    /**
     * Creates and returns a prepared statement
     * 
     * @param type $query
     * @return type
     */
    public function prepare_statement($query) {
        return ($this->mysqli->prepare($query));
    }

    /**
     * Returns the auto-generated key after an insertion
     * 
     * @return type
     */
    public function inserted_id() {
        return $this->mysqli->insert_id;
    }

    /**
     * Starts a transaction by setting the autocommit to false
     */
    public function start_transaction() {
        $this->mysqli->autocommit(false);
    }

    /**
     * Commits a transaction and returns the result.
     * 
     * @return type
     */
    public function commit() {
        //commiting and storing the response
        $commit = $this->mysqli->commit();

        //setting autocommit to on again
        $this->mysqli->autocommit(true);

        //now returning the commit result
        return $commit;
    }

    /**
     * Rollbacks a transaction and returns the result
     * 
     * @return type
     */
    public function rollback() {
        //rolling back and storing the response
        $rollback = $this->mysqli->rollback();

        //setting autocommit to on again
        $this->mysqli->autocommit(true);

        //now returning the rollback result
        return $rollback;
    }

}
