<?php
require_once "database_table.php";

class RecipeTable extends DatabaseTable {

    /**
     * Add a new recipe or update an existing one.
     *
     * Function adds a new recipe if recipe name does not exist. If it does exist and was deleted on todays date
     * the recipe is updated and its deletion_date set to null.
     *
     * @param  string $recipe_name   Name of the recipe.
     * @return boolean               Returns true on query success and false if category already exists.
     * @throws Exception             If query fails.
     */
    public static function add_recipe($recipe_name) {
        $date = date('Y-m-d');

        $sql = "SELECT * FROM Recipe
                WHERE name = '{$recipe_name}' AND deletion_date IS NULL";
        $result = parent::query($sql);
        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM Recipe
                    WHERE name = '{$recipe_name}' AND deletion_date = '{$date}'";
            $result = parent::query($sql);
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO Recipe (name, creation_date)
                        VALUES ('{$recipe_name}', '{$date}')";
            } else {
                $sql = "UPDATE Recipe SET deletion_date = NULL
                        WHERE name = '{$recipe_name}' and deletion_date = '{$date}'";
            }

            if (parent::query($sql)) {
                return true;
            } else {
                throw new Exception("add_recipe query failed");
            }
        } else {
            return false;
        }
    }

    /**
     * Delete a recipe in the database.
     *
     * Changes the recipe "deletion_date" to todays date. If successful, updates all items whos "recipe_id" matches the id
     * of the removed recipe to NULL.
     *
     * @param  int $recipe_id       Id of recipe to remove.
     * @return boolean              Return true if query is succesful and false if it fails.
     */
     public static function remove_recipe($recipe_id) {
        $sql = "UPDATE Recipe SET deletion_date = '" .date('Y-m-d'). "'
                WHERE id = '$recipe_id' and deletion_date IS NULL";
        if (parent::query($sql)) {
            $sql = "UPDATE Item SET recipe_id = NULL
                    WHERE deletion_date IS NULL AND recipe_id = '$recipe_id'";

            return parent::query($sql);
        }
    }

    /**
     * Get recipes from the database.
     *
     * @param  string   $date       Date till which categories will be retrieved.
     * @return object|false         Returns mysqli_result object if data is retrieved or false if query fails.
     */
     public static function get_recipes($date) {
        $sql = "SELECT * FROM Recipe
                WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' OR deletion_date IS NULL)
                ORDER BY name";

        return parent::query($sql);
    }
}
?>
