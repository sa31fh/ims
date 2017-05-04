<?php
require_once "database_table.php";

class CateringRecipeItemTable extends DatabaseTable{

    public static function add_recipe_items($recipe_id, $order_id) {
        $sql = "INSERT INTO CateringRecipeItems (item_id, recipe_id, order_id, base_quantity, price)
                SELECT item_id, $recipe_id, $order_id, quantity, price FROM RecipeItems
                INNER JOIN Item ON RecipeItems.item_id = Item.id
                WHERE RecipeItems.recipe_id = $recipe_id";

        return parent::query($sql);
    }

    public static function update_quantity_required($recipe_quantity, $recipe_id, $order_id) {
        $sql = "UPDATE CateringRecipeItems
                SET quantity_required = base_quantity * $recipe_quantity
                WHERE recipe_id = $recipe_id AND order_id = $order_id";

        return parent::query($sql);
    }

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

    public static function remove_recipe_items($recipe_id, $order_id) {
        $sql = "DELETE FROM CateringRecipeItems
                WHERE recipe_id = $recipe_id
                AND order_id = $order_id";

        return parent::query($sql);
    }
}
?>