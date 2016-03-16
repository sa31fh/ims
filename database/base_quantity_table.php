<?php
require_once "database_table.php";

class BaseQuantityTable extends DatabaseTable {

    /**
     * Get the base quantity value for a given item.
     *
     * @param  string $item_name name of the item.
     * @return float  $quantity  value if query is successful.
     * @throws Exception         if query fails.
     */
    public static function get_base_quantity($item_name) {
        $sql = "SELECT quantity FROM BaseQuantity
                WHERE item_id = (SELECT id FROM Item WHERE name='$item_name')";

        if($result = parent::query($sql)) {
            return $result->fetch_assoc()['quantity'];
        } else {
            throw new Exception("get_base_quantity query failed");
        }
    }

    /**
     * Set the base quantity for a given item.
     *
     * @param string $item_name name of the item.
     * @param float  $quantity  value of quantity to be set.
     * @return boolean          returns true on successful query or false if it fails.
     */
    public static function set_base_quantity($item_name, $quantity) {
        $sql = "INSERT INTO BaseQuantity (item_id, quantity)
                VALUES( (SELECT id FROM Item WHERE name='$item_name' AND deletion_date is null),
                        '$quantity')
                ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";

        return parent::query($sql);
    }

    /**
     * Update the base quantity value for a give item.
     *
     * @param  int  $item_id    id of the item of which the quantity will be updated.
     * @param  int  $quantity   new quantity value to be updated.
     * @return boolean          returns true on successful query or false if it fails.
     */
    public static function update_base_quantity($item_id, $quantity) {
        $sql = "INSERT INTO BaseQuantity (item_id, quantity)
                VALUES ('$item_id', '$quantity')
                ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";

        return parent::query($sql);
    }

    /**
     * Get estimated quantity for given item.
     *
     * @param  float $factor        sales factor needed to calculate the estimate quantity.
     * @param  string $item_name    item name used to get the base quantity.
     * @return float                returns the rounded estimated quantity value upto two decimal points.
     */
    public static function get_estimated_quantity($factor, $item_name) {
        return round(self::get_base_quantity($item_name) * $factor, 2);
    }
}
?>
