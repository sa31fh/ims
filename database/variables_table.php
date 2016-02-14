<?php 
require_once "data_base_table.php";

class VariablesTable extends DatabaseTable {

	public static function get_expected_sales() {

		$sql = "SELECT value FROM Variables WHERE name='ExpectedSales'";

		$result = parent::query($sql);
        return (int) $result->fetch_assoc()['value'];
	}

	public static function update_expected_sales($expected_sales) {

		$sql = "INSERT INTO Variables (name, value)  
	            VALUES ('ExpectedSales', '$expected_sales') 
	            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

        $result = parent::query($sql);
        return $result->fetch_assoc()['num'];
	}

	public static function get_base_sales() {

		 $sql = "SELECT value FROM Variables WHERE name='BaseSales'";

		 $result = parent::query($sql);
		 return (int) $result->fetch_assoc()['value'];
	}

	public static function update_base_sales($base_sales) {

		$sql = "INSERT INTO Variables (name, value)  
	            VALUES ('BaseSales', '$base_sales') 
	            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

        $result = parent::query($sql);
        return $result;
	}

}


 ?>