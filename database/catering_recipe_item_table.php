<?php
require_once "database_table.php";

class CateringRecipeItemTable extends DatabaseTable{

    public static function update_notes($notes, $item_id, $recipe_id, $order_id) {
        $sql = "INSERT INTO CateringRecipeItems (notes, item_id, recipe_id, order_id)
                VALUES('$notes', $item_id, $recipe_id, $order_id)
                ON DUPLICATE KEY UPDATE
                notes= VALUES(notes), item_id = VALUES(item_id), recipe_id = VALUES(recipe_id),
                order_id = VALUES(order_id)";

        return parent::query($sql);
    }

    public static function update_quantity_delivered($quantity, $item_id, $recipe_id, $order_id) {
        $sql = "INSERT INTO CateringRecipeItems (quantity_delivered, item_id, recipe_id, order_id)
                VALUES($quantity, $item_id, $recipe_id, $order_id)
                ON DUPLICATE KEY UPDATE
                quantity_delivered= VALUES(quantity_delivered), item_id = VALUES(item_id), recipe_id = VALUES(recipe_id),
                order_id = VALUES(order_id)";

        return parent::query($sql);
    }

    public static function update_invoice_notes($notes, $item_id, $recipe_id, $order_id) {
        $sql = "INSERT INTO CateringRecipeItems (invoice_notes, item_id, recipe_id, order_id)
                VALUES('$notes', $item_id, $recipe_id, $order_id)
                ON DUPLICATE KEY UPDATE
                invoice_notes= VALUES(invoice_notes), item_id = VALUES(item_id), recipe_id = VALUES(recipe_id),
                order_id = VALUES(order_id)";

        return parent::query($sql);
    }
}
?>