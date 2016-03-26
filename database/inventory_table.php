<?php
require_once "database_table.php";

class InventoryTable extends DatabaseTable {

    public static function get_inventory($category_id, $date) {
        $sql = "SELECT T2.item_id AS id, T2.item_name AS name, T2.item_unit AS unit, IFNULL(T1.quantity, \"-\") AS quantity, T1.notes AS notes FROM
                (SELECT * FROM Inventory
                WHERE Inventory.date = '{$date}') AS T1
                RIGHT JOIN
                (SELECT Item.id AS item_id, Item.name AS item_name, Item.unit AS item_unit, Item.order_id FROM Item
                INNER JOIN Category ON Item.category_id = Category.id
                WHERE Category.id = {$category_id}
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL))
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))) AS T2 ON T2.item_id = T1.item_id
                ORDER BY T2.order_id";

        return parent::query($sql);
    }

    public static function update_inventory($date, $item_id, $quantity, $item_note) {
        $sql = "INSERT INTO Inventory (`date`, item_id, quantity, notes)
                VALUES ('$date', '$item_id', '$quantity', '$item_note')
                ON DUPLICATE KEY UPDATE
                `date`= VALUES(`date`), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)";

        return parent::query($sql);
    }
}
?>
