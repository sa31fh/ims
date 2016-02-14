<?php 
class DatabaseTable {
    private static $servername = "localhost";
    private static $username = "root";
    private static $password = null;
    private static $dbname = "new_inventory";

    private static $conn = null;

    final protected static function query($sql) {
        if (self::$conn == null) {
            self::$conn = new mysqli(self::$servername, self::$username,
                                     self::$password, self::$dbname);
            if(self::$conn->connect_error){
                die("Connection failed: " .self::$conn->connect_error);
            }
        }
        return self::$conn->query($sql);
    }
}
?>
