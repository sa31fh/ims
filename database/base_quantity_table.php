<?php 
require_once "database_table.php";

class BaseQuantityTable extends DatabaseTable {

    public static function get_base_quantity($item_name) {
        $sql = "SELECT quantity FROM BaseQuantity  
                WHERE item_id = (SELECT id FROM Item WHERE name='$item_name')";

        if($result = parent::query($sql)) {
            return (int) $result->fetch_assoc()['quantity'];
        } else {
            return false;
        }
    }

    public static function update_base_quantity($item_id, $quantity) {
        $sql = "INSERT INTO BaseQuantity (item_id, quantity)  
                VALUES ('$item_id', '$quantity') 
                ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";
                
        return parent::query($sql);
    }
   
    public static function get_estimated_quantity($factor, $item_name) {
        return round(self::get_base_quantity($item_name) * $factor, 2);
    }
}
?>
