<?php

/**
 * The User Entity Class, all the user-oriented functionality
 */
require_once ('mysql.php');

class User {

    public $userID;
    public $username;
    public $password;
    public $email;
    public $uuid;
    public $last_login;
    public $date_created;
    public $date_modified;

    /**
     * Inserts a new user in the DB
     * Returns the auto-generated ID
     * 
     * @global type $mysql
     * @return boolean
     * @throws Exception
     */
    public function insert() {
        global $mysql;
        $query = "
            INSERT INTO user (Username, Password, Email, UUID, DateCreated) VALUES 
            (?, ?, ?, ?, CURRENT_TIMESTAMP) ";

        $types = "ssss";
        $values = array(&$this->username, &$this->password, &$this->email, &$this->uuid);

        $res = $mysql->smart_stmt($query, $types, $values);
        return $res;
    }

    /**
     * Authenticates a user's credentials.
     * If the authentication is successful, returns TRUE
     * If sth goes wrong or the authentication is not successful, returns FALSE
     * 
     * @global type $mysql
     * @return boolean
     * @throws Exception
     */
    public function authenticate() {
        global $mysql;
        $num = -1;

        $query = "
            SELECT COUNT(*) AS num 
            FROM user 
            WHERE Username = ? AND Password = ? ";

        $types = "ss";
        $values = array(&$this->username, &$this->password);

        $res = $mysql->smart_stmt($query, $types, $values);
        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $num = $row['num'];
        }

        if ($num == 1)
            return true;
        else
            return false;
    }

    /**
     * Fetches the details of a user object from the DB
     * 
     * $param = "id"
     * $param = "username"
     * 
     * @global type $mysql
     * @param type $param
     * @return boolean|\User
     * @throws Exception
     */
    public function fetch($param) {
        global $mysql;

        $query = "
            SELECT userID, Username, Email, UUID 
            FROM user 
            WHERE ";

        if (strcmp($param, "id") == 0) {
            $whereS = "userID = ? ";
            $types = "i";
            $values = array(&$this->userID);
        } elseif (strcmp($param, "username") == 0) {
            $whereS = "Username = ? ";
            $types = "s";
            $values = array(&$this->username);
        }

        $query .= $whereS;

        $res = $mysql->smart_stmt($query, $types, $values);

        if (!$res) {
            $result = false;
        } else {
            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $u = new User();

                $u->userID = $row['userID'];
                $u->username = $row['Username'];
                $u->email = $row['Email'];
                $u->uuid = $row['UUID'];
            }
            $result = $u;
        }

        return $result;
    }

}
