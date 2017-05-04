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
                T2.rounding_factor AS rounding_factor, T1.expected_quantity AS expected_quantity, T1.has_deviation,
                T2.cat_id FROM
                (SELECT * FROM Inventory
                WHERE Inventory.date = '{$date}') AS T1
                RIGHT JOIN
                (SELECT Category.id AS cat_id, Item.id AS item_id, Item.name AS item_name, Item.unit AS item_unit,
                        Item.order_id, deviation, rounding_option, rounding_factor FROM Item
                INNER JOIN Category ON Item.category_id = Category.id
                    WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL))
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
                `date`= VALUES(`date`), item_id = VALUES(item_id), quantity = VALUES(quantity),
                notes = VALUES(notes)";

        return parent::query($sql);
    }

    public static function update_expected_quantity($quantity, $item_id, $date) {
        $sql = "INSERT INTO Inventory (`date`, item_id, expected_quantity)
                VALUES ('$date', '$item_id', $quantity)
                ON DUPLICATE KEY UPDATE
                `date`= VALUES(`date`), item_id = VALUES(item_id), expected_quantity = VALUES(expected_quantity)";

        return parent::query($sql);
    }

    public static function update_item_deviation($deviation, $item_id, $date) {
        $sql = "INSERT INTO Inventory (`date`, item_id, has_deviation)
                VALUES ('$date', '$item_id', $deviation)
                ON DUPLICATE KEY UPDATE
                `date`= VALUES(`date`), item_id = VALUES(item_id), has_deviation = VALUES(has_deviation)";

        return parent::query($sql);
    }

    public static function update_quantity_required($quantity, $item_id, $date) {
        $sql = "INSERT INTO Inventory (item_id, quantity_required, `date`)
                VALUES ('$item_id', $quantity, '$date')
                ON DUPLICATE KEY UPDATE
                item_id = VALUES(item_id), quantity_required = VALUES(quantity_required), `date` = VALUES(`date`)";

        return parent::query($sql);

    }

    public static function get_inventory_with_deviation($date) {
        $sql = "SELECT IFNULL(Inventory.quantity, null) AS quantity, Item.id, Item.category_id, Item.name,
                        Item.order_id, Item.rounding_option,Item.rounding_factor, Item.unit, Item.deviation,
                        Category.name AS category_name, Inventory.expected_quantity, Inventory.has_deviation FROM Item
                INNER JOIN Category ON Category.id = Item.category_id
                LEFT JOIN
                (SELECT * FROM Inventory WHERE Inventory.date = '$date') AS Inventory ON Item.id = Inventory.item_id
                WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL))
                AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))
                ORDER BY Category.order_id ASC, Item.order_id ASC";

        return parent::query($sql);
    }

     public static function get_search_inventory($date) {
        $sql = "SELECT IFNULL(Inventory.quantity, null) AS quantity, Item.id, Item.category_id, Item.name,
                        Item.order_id, Item.rounding_option, Item.rounding_factor, Item.unit, Item.deviation,
                        Inventory.notes, Category.name AS category_name, Inventory.expected_quantity,
                        Inventory.has_deviation FROM Item
                INNER JOIN Category ON Category.id = Item.category_id
                LEFT JOIN
                (SELECT * FROM Inventory WHERE Inventory.date = '$date') AS Inventory ON Item.id = Inventory.item_id
                WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL))
                AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))
                ORDER BY Category.order_id ASC, Item.order_id ASC";

        return parent::query($sql);
    }

    public static function get_inventory_quantity($date) {
        $sql = "SELECT * FROM Inventory
                WHERE date = '$date'
                AND quantity IS NOT NULL";

        return parent::query($sql);
    }

    public static function update_quantity_delivered($quantity, $item_id, $date) {
        $sql = "INSERT INTO Inventory (item_id, quantity_delivered, `date`)
                VALUES ('$item_id', $quantity, '$date')
                ON DUPLICATE KEY UPDATE
                item_id = VALUES(item_id), quantity_delivered = VALUES(quantity_delivered), `date` = VALUES(`date`)";

        return parent::query($sql);

    }

    public static function update_invoice_note($note, $item_id, $date) {
        $sql = "INSERT INTO Inventory (item_id, invoice_notes, `date`)
                VALUES ('$item_id', '$note', '$date')
                ON DUPLICATE KEY UPDATE
                item_id = VALUES(item_id), invoice_notes = VALUES(invoice_notes), `date` = VALUES(`date`)";


        return parent::query($sql);

    }

    public static function update_cost_required($cost, $item_id, $date) {
        $sql = "UPDATE Inventory
                SET cost_required = $cost
                WHERE item_id = $item_id
                AND `date` = '$date'";

        return parent::query($sql);
    }

    public static function update_cost_delivered($cost, $item_id, $date) {
        $sql = "UPDATE Inventory
                SET cost_delivered = $cost
                WHERE item_id = $item_id
                AND `date` = '$date'";

        return parent::query($sql);
    }
}
?>
