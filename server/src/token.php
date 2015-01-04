<?php

/**
 * The Token Entity Class, all the token-oriented functionality
 */
require_once ('mysql.php');

class Token {

    public $tokenID;
    public $token;
    public $user_uuid;
    public $session_uuid;
    public $date_created;
    public $date_modified;

    /**
     * Inserts a new token in the DB
     * Returns the auto-generated ID
     * 
     * @global type $mysql
     * @return boolean
     * @throws Exception
     * @throws Exceptionh
     */
    public function insert_token() {
        global $mysql;
        $query = "
            INSERT INTO token (Token, UserUUID, SessionUUID, DateCreated) VALUES 
            (?, ?, ?, CURRENT_TIMESTAMP) ";

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            //checking if all mandatory params are set
            if ($this->token == null || $this->user_uuid == null || $this->session_uuid == null)
                throw new Exception("Missing Params");

            $stmt->bind_param("sss", $this->token, $this->user_uuid, $this->session_uuid);
            $stmt->execute();

            if ($stmt->affected_rows != 1)
                throw new Exception("Affected Rows: " . $stmt->affected_rows);

            $result = $mysql->inserted_id();
        } catch (Exception $ex) {
            $result = false;
        } finally {
            if ($stmt != false)
                $stmt->close();
            return $result;
        }
    }

    /**
     * Deletes a token from the database 
     * 
     * @global type $mysql
     * @return boolean
     * @throws Exception
     */
    public function delete_token() {
        global $mysql;
        $query = "
            DELETE FROM Token 
            WHERE tokenID = ? ";

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            //checking if all mandatory params are set
            if ($this->tokenID == null)
                throw new Exception("Missing Params");

            $stmt->bind_param("i", $this->tokenID);
            $stmt->execute();

            if ($stmt->affected_rows != 1)
                throw new Exception("Affected Rows: " . $stmt->affected_rows);

            $result = true;
        } catch (Exception $ex) {
            $result = false;
        } finally {
            if ($stmt != false)
                $stmt->close();
            return $result;
        }
    }

}
