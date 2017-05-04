<?php
require_once "database_table.php";

class CateringItemTable extends DatabaseTable{

    public static function add_item($item_id, $order_id) {
        $sql = "INSERT INTO CateringItems (item_id, order_id, price)
                SELECT $item_id, $order_id, price FROM Item
                WHERE Item.id = $item_id";

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

    public static function get_items_with_recipes($order_id) {
        $sql = "SELECT * FROM (
                    SELECT  CRI.item_id,
                            coalesce(SUM(CRI.quantity_required) + Cat_items.quantity_required, SUM(CRI.quantity_required), Cat_items.quantity_required) AS quantity_required,
                            IFNULL(Cat_items.quantity_delivered, CRI.quantity_delivered) AS quantity_delivered,
                            IFNULL(Cat_items.notes, CRI.notes) AS notes,
                            IFNULL(Cat_items.invoice_notes, CRI.invoice_notes) AS invoice_notes,
                            CateringRecipes.recipe_id, CRI.price FROM CateringRecipes
                    LEFT JOIN (SELECT * FROM CateringRecipeItems WHERE order_id = $order_id) AS CRI
                    ON CateringRecipes.recipe_id = CRI.recipe_id
                    LEFT JOIN (SELECT * FROM CateringItems WHERE order_id = $order_id) AS Cat_items
                    ON CRI.item_id = Cat_items.item_id
                    WHERE CateringRecipes.order_id = $order_id
                    GROUP BY item_id
                    UNION ALL
                    SELECT CateringItems.item_id, quantity_required, quantity_delivered, notes, invoice_notes, null, price FROM CateringItems
                    WHERE CateringItems.item_id NOT IN (SELECT item_id FROM RecipeItems)
                    AND order_id = '$order_id') AS CombinedTable
                INNER JOIN
                (SELECT Item.id AS item_id, Item.name AS item_name, Category.name AS category_name,
                        Category.order_id AS cat_order, Item.order_id AS  item_order, Item.unit FROM Item
                INNER JOIN Category on Item.category_id = Category.id) AS ItemTable
                ON CombinedTable.item_id = ItemTable.item_id
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

    public static function update_invoice_notes($notes, $item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET invoice_notes = '$notes'
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function check_item($item_id, $order_id) {
        $sql = "SELECT * FROM CateringItems
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql)->num_rows;
    }
}
?>