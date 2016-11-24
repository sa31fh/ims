<?php
require_once "database_table.php";

class InventoryTable extends DatabaseTable {

    /**
     * Get inventory and item data for a given category.
     *
     * Gets items and their inventory data for a given category till a given date.
     *
     * @param  int      $category_id    Id of the category to get items of.
     * @param  string   $date           Date till which data will be returned.
     * @return object|false             Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_inventory($category_id, $date) {
        $sql = "SELECT T2.item_id AS id, T2.item_name AS name, T2.item_unit AS unit, IFNULL(T1.quantity, null) AS quantity, 
                T1.notes AS notes, T2.deviation AS deviation, T2.rounding_option AS rounding_option,
                T2.rounding_factor AS rounding_factor FROM
                (SELECT * FROM Inventory
                WHERE Inventory.date = '{$date}') AS T1
                RIGHT JOIN
                (SELECT Item.id AS item_id, Item.name AS item_name, Item.unit AS item_unit,
                        Item.order_id, deviation, rounding_option, rounding_factor FROM Item
                INNER JOIN Category ON Item.category_id = Category.id
                WHERE Category.id = {$category_id}
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL))
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))) AS T2 ON T2.item_id = T1.item_id
                ORDER BY T2.order_id";

        return parent::query($sql);
    }

    /**
     * Update inventory entry if exists or create a new one.
     *
     * @param  string   $date        Date value to update or add.
     * @param  int      $item_id     Id of item to add if id doesn't exist.
     * @param  int      $quantity    Quantity value to update or add.
     * @param  string   $item_note   Note value to update or add.
     * @return boolean               Returns true on query success or false if it fails.
     */
    public static function update_inventory($date, $item_id, $quantity, $item_note) {
        $sql = "INSERT INTO Inventory (`date`, item_id, quantity, notes)
                VALUES ('$date', '$item_id', $quantity, '$item_note')
                ON DUPLICATE KEY UPDATE
                `date`= VALUES(`date`), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)";

        return parent::query($sql);
    }
}
?>
