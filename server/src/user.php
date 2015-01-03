<?php

/**
 * The User Entity Class, all the user-oriented functionality
 */
require_once ('db.php');

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

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            //checking if mandatory params are set
            if ($this->username == null || $this->password == null || $this->uuid == null)
                throw new Exception("Missing Params");

            $stmt->bind_param("ssss", $this->username, $this->password, $this->email, $this->uuid);
            $stmt->execute();

            $stmt->close();
            return $mysql->inserted_id();
        } catch (Exception $ex) {
            return false;
        }
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

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            //checking if mandatory params are set
            if ($this->username == null || $this->password == null)
                throw new Exception("Missing Params");

            $stmt->bind_param("ss", $this->username, $this->password);
            $stmt->execute();

            $res = $stmt->get_result();
            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $num = $row['num'];
            }

            if ($num == -1)
                throw new Exception("Something went wrong while processing the query");
            elseif ($num != 1)
                throw new Exception("Wrong Credentials");

            $stmt->close();
            return true;
        } catch (Exception $ex) {
            return false;
        }
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

        if (strcmp($param, "id") == 0)
            $whereS = "userID = ? ";
        elseif (strcmp($param, "username") == 0)
            $whereS = "Username = ? ";

        $query = "
            SELECT userID, Username, Email, UUID 
            FROM user 
            WHERE ";

        try {
            $stmt = $mysql->prepare_statement($query);

            //checking if query is well written
            if (!$stmt)
                throw new Exception("Invalid Query: " . $query);

            if (strcmp($param, "id") == 0)
                $stmt->bind_param("i", $this->userID);
            elseif (strcmp($param, "username") == 0)
                $stmt->bind_param("s", $this->username);
            else
                throw new Exception("No parameter passed");

            $stmt->execute();

            $res = $stmt->get_result();
            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                $u = new User();

                $u->userID = $row['userID'];
                $u->username = $row['Username'];
                $u->email = $row['Email'];
                $u->uuid = $row['UUID'];
            }
            $stmt->close();
            return $u;
        } catch (Exception $ex) {
            return false;
        }
    }

}
