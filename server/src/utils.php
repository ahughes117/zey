<?php

class Utils {

    /**
     * Wrapper functions that hashes the password securely by BCRYPT
     * 
     * @param type $password
     * @return type
     */
    public static function hash_password($password) {
        return password_hash($password, PASSWORD_BCRYPT_DEFAULT_COST);
    }

    /**
     * Wrapper function that verifies a hashed password
     * 
     * @param type $password
     * @param type $hash
     * @return type
     */
    public static function password_verify($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * taken from here:http://www.php.net/manual/en/function.uniqid.php#94959
     * 
     * @return type 
     */
    function gen_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}
