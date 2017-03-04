<?php
require_once "database_table.php";

class CateringRecipeTable extends DatabaseTable{

    public static function add_recipe($recipe_id, $order_id) {
        $sql = "INSERT INTO CateringRecipes (recipe_id, order_id)
                VALUES('$recipe_id', '$order_id')";

        return parent::query($sql);
    }

    public static function get_recipes($order_id) {
        $sql = "SELECT Recipe.name, recipe_id, quantity_required, notes FROM CateringRecipes
                INNER JOIN Recipe
                ON Recipe.id = recipe_id
                WHERE CateringRecipes.order_id = '$order_id'
                ORDER BY name";

        return parent::query($sql);
    }

    public static function remove_recipe($recipe_id, $order_id) {
        $sql = "DELETE FROM CateringRecipes
                WHERE recipe_id = '$recipe_id'
                AND order_id = '$order_id'";

        return parent::query($sql);
    }

    public static function update_quantity($quantity, $recipe_id, $order_id) {
        $sql = "UPDATE CateringRecipes
                SET quantity_required = $quantity
                WHERE recipe_id = $recipe_id
                AND order_id = $order_id";

        return parent::query($sql);
    }

    public static function update_notes($notes, $recipe_id, $order_id) {
        $sql = "UPDATE CateringRecipes
                SET notes = '$notes'
                WHERE recipe_id = $recipe_id
                AND order_id = $order_id";

        return parent::query($sql);
    }
}
?>