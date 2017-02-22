<?php
require_once "database_table.php";

class CateringItemTable extends DatabaseTable{

    public static function add_item($item_id, $order_id) {
        $sql = "INSERT INTO CateringItems (item_id, order_id)
                VALUES('$item_id', '$order_id')";

        return parent::query($sql);
    }

    public static function get_items($order_id) {
        $sql = "SELECT itemId AS item_id, item_name, category_name, Items.unit,
                CateringItems.quantity_required, CateringItems.quantity_delivered,
                CateringItems.invoice_notes, CateringItems.notes, Items.price FROM CateringItems
                INNER JOIN
                    (SELECT Item.id AS itemId, Item.name AS item_name, Category.name AS category_name,
                            Category.order_id AS cat_order, Item.order_id AS  item_order, Item.unit, Item.price FROM Item
                    INNER JOIN Category on Item.category_id = Category.id) AS Items
                ON Items.itemId = item_id
                WHERE order_id = '$order_id'
                ORDER BY cat_order ASC, item_order ASC";

        return parent::query($sql);
    }

    public static function remove_item($item_id, $order_id) {
        $sql = "DELETE FROM CateringItems
                WHERE item_id = '$item_id'
                AND order_id = '$order_id'";

        return parent::query($sql);
    }

    public static function update_quantity($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET quantity_required = $quantity
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_notes($notes, $item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET notes = '$notes'
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_quantity_delivered($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET quantity_delivered = $quantity
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_Invoice_notes($notes, $item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET invoice_notes = '$notes'
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }
}
?>