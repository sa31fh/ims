<?php 
require_once "data_base_table.php";

class BaseQuantityTable extends DatabaseTable {

	public static function get_base_quantity($item_name) {

		$sql = "SELECT quantity FROM BaseQuantity  
            	WHERE item_id = (SELECT id FROM Item WHERE name='$item_name')";
        $result = parent::query($sql);
        return (int) $result->fetch_assoc()['quantity'];
	}

	public static function update_base_quantity($item_id, $quantity) {

		$sql = "INSERT INTO BaseQuantity (item_id, quantity)  
	            VALUES ('$item_id' , '$quantity') 
	            ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";

        $result = parent::query($sql);
        return $result;
	}
   
	public static function get_estimated_quantity($factor, $item_name) {
		return round(self::get_base_quantity($item_name) * $factor,2);
	}
}

?>