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
     * Boilerplate code for executing statements and fetching their result
     * Should only be trusted for simple statements SELECT, UPDATE, INSERT, DELETE
     * 
     * @global MySQL $mysql
     * @param type $query
     * @param type $types
     * @param type $values
     * @return boolean
     * @throws Exception
     */
    public function smart_stmt($query, $types, $values) {
        global $mysql;

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            //binds the values
            call_user_func_array(
                    array($stmt, 'bind_param'), array_merge(
                            array($types), $values
                    )
            );
            $stmt->execute();

            if (strpos($query, "SELECT") !== false)
                $result = $stmt->get_result();
            elseif (strpos($query, "INSERT") !== false)
                $result = $mysql->inserted_id();
            elseif (strpos($query, "UPDATE") !== false || strpos($query, "DELETE") !== false)
                $result = true;
            
        } catch (Exception $ex) {
            $result = false;
        } finally {
            if ($stmt != false)
                $stmt->close();
            return $result;
        }
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
