<?php 
require_once "data_base_table.php";

class UserTable extends DatabaseTable{

	public static function add_new_user($user_name, $first_name, $last_name, $password, $user_role) {
		
		$sql = "SELECT username FROM User  
                WHERE  username = '$user_name'";
        $result = parent::query($sql);
        $row = $result->fetch_assoc();
        if ($row["username"] == $user_name) {
        echo '<br>Error: Username already exists!<br>';
        return False;
  		}

        $sql = "INSERT INTO User (username, first_name, last_name, password_hash, userrole_id) 
	            VALUES('$user_name', '$first_name', '$last_name', '" .password_hash($password, PASSWORD_DEFAULT). "', 
	                  (SELECT id FROM UserRole WHERE role='{$user_role}'))";
	    $result = parent::query($sql);
	}

	public static function get_users() {

		$sql = "SELECT * FROM User 
		        INNER JOIN UserRole ON User.userrole_id = UserRole.id
		        ORDER BY username ASC";
        $result = parent::query($sql);
        return $result;
	}

	public static function get_user_details($user) {

		$sql = "SELECT * FROM User 
        		INNER JOIN UserRole ON User.userrole_id = UserRole.id
        		WHERE username = '$user'";
        $result = parent::query($sql);
        return $result;
	}

	public static function update_user_details($current_username, $new_username, $first_name, $last_name, $time_zone) {

		$sql = "UPDATE User 
	            SET username = '$new_username',
	                first_name = '$first_name',
	                last_name = '$last_name',
	                time_zone = '$time_zone'
	            WHERE username ='$current_username'";
        $result = parent::query($sql);
        return $result;
	}

	public static function delete_user($user_name) {

		$sql = "DELETE FROM User WHERE username = '$user_name'";
		$result = parent::query($sql);
		return $result;
	}

	public static function verify_credentials($user_name, $password) {

		$sql = "SELECT * FROM User
	            INNER JOIN UserRole ON User.userrole_id = UserRole.id
	            WHERE username='$user_name'";
        if ($result = parent::query($sql)) {

        	$row = $result->fetch_assoc();

        	if ($row == null OR !password_verify($password, $row['password_hash']) ){
	           return false;
	        } else {
	           return true;
	        }
        }
	}

	public static function update_user_password($user_name, $new_password) {

		$sql = "UPDATE User
	            SET password_hash='" .password_hash($new_password, PASSWORD_DEFAULT). "' 
	            WHERE username='$user_name'";
        $result = parent::query($sql);
        return $result;
	}

	public static function set_session_variables($user_name) {

		$sql = "SELECT * FROM User
	            INNER JOIN UserRole ON User.userrole_id = UserRole.id
	            WHERE username='$user_name'";

        if ($result = parent::query($sql)) {
        
	        $row = $result->fetch_assoc();
	        $_SESSION["username"] = $user_name;
	        $_SESSION["userrole"] = $row["role"];
	        if (!empty($row["time_zone"])) {
	            $_SESSION["timezone"] = $row["time_zone"];
	        } else {
	            $_SESSION["timezone"] = date_default_timezone_get();
	        }
	    } else {
	        echo "set_session_variables query failed";
	    }
	}

}

?>
