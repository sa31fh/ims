<?php
require_once "database_table.php";

class RecipeItemTable extends DatabaseTable {

    /**
     * Add new item to a given recipe.
     *
     * @param int $item_id      Id of item to add.
     * @param int $recipe_id    Id of recipe to item add to.
     * @return boolean          Returns true on query success and false if item already exists.
     */
    public static function add_recipe_item($item_id, $recipe_id) {
        $sql = "INSERT INTO RecipeItems (item_id, recipe_id)
                VALUES ('$item_id', '$recipe_id')";

        return parent::query($sql);
    }

    /**
     * Get all items for given recipe.
     *
     * @param  int $recipe_id    Id of recipe to get items for.
     * @return object|false      Returns mysqli_result object if data is retrieved or false if query fails.
     */
    public static function get_recipe_items($recipe_id) {
        $sql = "SELECT Item.name, RecipeItems.quantity, RecipeItems.id FROM RecipeItems
                INNER JOIN Item ON Item.id = item_id
                WHERE recipe_id = '$recipe_id'";

        return parent::query($sql);
    }

    /**
     * Delete item from given recipe.
     *
     * @param  int $item_id      Id of item to delete.
     * @param  int $recipe_id    Id of recipe to delete item from.
     * @return boolean          Returns true on query success and false if item already exists.
     */
    public static function delete_recipe_item($item_id, $recipe_id) {
        $sql = "DELETE FROM RecipeItems
                WHERE item_id = '$item_id'
                AND recipe_id = '$recipe_id'";

        return parent::query($sql);

    }

    /**
     * Update quantity for given recipe table item.
     *
     * @param  int $quantity                Value of quantity to update.
     * @param  int $recipe_inventory_id     Id of table row to update.
     * @return boolean                      Returns true on query success and false if item already exists.
     */
    public static function update_recipe_inventory_quantity($recipe_inventory_id, $quantity) {
        $sql = "UPDATE RecipeItems
                SET quantity = '$quantity'
                WHERE id = '$recipe_inventory_id'";

        return parent::query($sql);
    }
}
?>