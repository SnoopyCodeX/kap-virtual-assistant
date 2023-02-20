<?php
include_once('db.inc.php');

class Auth {

    private static $COOKIE_KEY = 'askkap_user';



    /**
     * Checks if the user is logged in
     * by verifying if the cookie for 
     * log in exists in his computer.
     * 
     * @return bool
     */
    public static function isAuthenticated() : bool {
        return (isset($_COOKIE[Auth::$COOKIE_KEY]));
    }

    /**
     * Creates login cookie
     * using the user's id
     * 
     * The default expiration of the 
     * cookie is 30 days.
     * 
     * @param string $userID The id of the user that will be used as the value of the cookie
     * @param string $path The path where the cookie is accessible, by default '/' -> the entire domain of the website
     * @param string $expiresAt The expiration of the cookie, default is 30 days
     * @return void
     */
    public static function createLoginCookie(int $userID, string $path = '/', string $expiresAt = "30d") : void {
        require_once('./includes/timeutils.inc.php');

        $expiresAt = time() + TimeUtils::toSeconds($expiresAt);
        setcookie(Auth::$COOKIE_KEY, $userID, $expiresAt, $path);
    }

    /**
     * Returns the value of the 
     * user's login cookie
     * 
     * @return string|null
     */
    public static function getLoginCookie() : ?string {
        if(!Auth::isAuthenticated())
            return null;

        return $_COOKIE[Auth::$COOKIE_KEY];
    }

    /**
     * Clears the user's login cookie
     * 
     * @return void
     */
    public static function clearLoginCookie() : void {
        if(!Auth::isAuthenticated())
            return;

        setcookie(Auth::$COOKIE_KEY, Auth::getLoginCookie(), time() - 60, '/');
    }

    /**
     * Checks if the user is an admin
     * 
     * @return ?bool
     */
    public static function isAdmin() : ?bool {
        global $conn, $usersTable;

        if(!Auth::isAuthenticated())
            return null;

        $userID = Auth::getLoginCookie();
        $__getUserResult__ = $conn->query("SELECT * FROM $usersTable WHERE id='$userID'");

        if($__getUserResult__->num_rows == 0)
            return false;

        $userRole = $__getUserResult__->fetch_assoc()['role'];

        return ($userRole == 1 || $userRole == 2);
    }
}

?>