<?php 
require_once "data_base_table.php";

class UserRoleTable extends DatabaseTable {

	public static function get_roles() {

		$sql = "SELECT * FROM UserRole";

		$result = parent::query($sql);
		return $result;
	}

	public static function update_user_role($user_name, $role) {

		$sql = "UPDATE User 
            SET userrole_id= (SELECT id FROM UserRole WHERE role='$role') 
            WHERE username='$user_name'";
        $result = parent::query($sql);
        return $result;
	}

}

 ?>