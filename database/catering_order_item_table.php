<?php
require_once "database_table.php";

class CateringOrderItemTable extends DatabaseTable{

    public static function add_item($item_id, $order_id) {
        $sql = "INSERT INTO CateringOrderItems (item_id, order_id, price)
                SELECT $item_id, $order_id, price FROM CateringItems
                WHERE CateringItems.id = $item_id";

        return parent::query($sql);
    }

    public static function get_items($order_id) {
        $sql = "SELECT itemId AS item_id, item_name, category_name, Items.unit,
                Items.rounding_option, Items.rounding_factor, CateringOrderItems.quantity_required, 
                CateringOrderItems.quantity_delivered,CateringOrderItems.quantity_custom, 
                CateringOrderItems.invoice_notes, CateringOrderItems.notes, Items.price FROM CateringOrderItems
                INNER JOIN
                    (SELECT CateringItems.id AS itemId, CateringItems.name AS item_name, CateringCategory.name AS category_name,
                            CateringItems.rounding_option, CateringItems.rounding_factor, CateringCategory.order_id AS cat_order, 
                            CateringItems.order_id AS  item_order, CateringItems.unit, CateringItems.price FROM CateringItems
                    INNER JOIN CateringCategory on CateringItems.category_id = CateringCategory.id) AS Items
                ON Items.itemId = item_id
                WHERE order_id = '$order_id'
                ORDER BY cat_order ASC, item_order ASC";

        return parent::query($sql);
    }

    public static function get_items_with_recipes($order_id) {
        $sql = "SELECT * FROM (
                SELECT * FROM (
                    SELECT  CRI.item_id,
                            coalesce(SUM(CRI.quantity_required) + IFNULL(Cat_items.quantity_custom, Cat_items.quantity_required),
                                     SUM(CRI.quantity_required), IFNULL(Cat_items.quantity_custom, Cat_items.quantity_required))
                                     AS quantity_required,
                            IFNULL(Cat_items.quantity_delivered, CRI.quantity_delivered) AS quantity_delivered,
                            IFNULL(Cat_items.quantity_received, CRI.quantity_received) AS quantity_received,
                            IFNULL(Cat_items.notes, CRI.notes) AS notes,
                            IFNULL(Cat_items.invoice_notes, CRI.invoice_notes) AS invoice_notes,
                            CateringRecipes.recipe_id, Cat_items.price, 
                            coalesce(SUM(CRI.cost_required) + Cat_items.cost_required, SUM(CRI.cost_required), Cat_items.cost_required)
                                     AS cost_required,
                            IFNULL(Cat_items.cost_delivered, CRI.cost_delivered) AS cost_delivered
                            FROM CateringRecipes
                    LEFT JOIN (SELECT * FROM CateringRecipeItems WHERE order_id = $order_id) AS CRI
                    ON CateringRecipes.recipe_id = CRI.recipe_id
                    LEFT JOIN (SELECT * FROM CateringOrderItems WHERE order_id = $order_id) AS Cat_items
                    ON CRI.item_id = Cat_items.item_id
                    WHERE CateringRecipes.order_id = $order_id
                    GROUP BY item_id
                    UNION
                    SELECT CateringOrderItems.item_id, IFNULL(quantity_custom, quantity_required) AS quantity_required, quantity_delivered, quantity_received, notes, invoice_notes, null, price, cost_required, cost_delivered FROM CateringOrderItems
                    WHERE order_id = '$order_id' 
                    GROUP BY item_id) AS CombinedTable
                INNER JOIN
                (SELECT CateringItems.id AS ci_item_id, CateringItems.name AS item_name, CateringCategory.name AS category_name,
                        CateringCategory.order_id AS cat_order, CateringItems.order_id AS  item_order, CateringItems.unit FROM CateringItems
                INNER JOIN CateringCategory on CateringItems.category_id = CateringCategory.id) AS ItemTable
                ON CombinedTable.item_id = ItemTable.ci_item_id
                ORDER BY cat_order ASC, item_order ASC )AS t1 GROUP BY item_id";

        return parent::query($sql);
    }

    public static function remove_item($item_id, $order_id) {
        $sql = "DELETE FROM CateringOrderItems
                WHERE item_id = '$item_id'
                AND order_id = '$order_id'";

        return parent::query($sql);
    }

    public static function update_quantity_required($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET quantity_required = $quantity
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_quantity_custom($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET quantity_custom = $quantity
                WHERE item_id = '$item_id'
                AND order_id = '$order_id'";

        return parent::query($sql);

    }

    public static function update_cost_required($cost, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET cost_required = $cost
                WHERE item_id = $item_id
                AND order_id = '$order_id'";

        return parent::query($sql);
    }

    public static function update_cost_delivered($cost, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET cost_delivered = $cost
                WHERE item_id = $item_id
                AND order_id = '$order_id'";

        return parent::query($sql);
    }

    public static function update_notes($notes, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET notes = '$notes'
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_quantity_delivered($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET quantity_delivered = $quantity
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_quantity_received($quantity, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET quantity_received = $quantity
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_invoice_notes($notes, $item_id, $order_id) {
        $sql = "UPDATE CateringOrderItems
                SET invoice_notes = '$notes'
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function check_item($item_id, $order_id) {
        $sql = "SELECT * FROM CateringOrderItems
                WHERE item_id = $item_id
                AND order_id = $order_id";

        return parent::query($sql)->num_rows;
    }
}
?>