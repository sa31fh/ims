<?php
require_once "database_table.php";

class CateringCategoryTable extends DatabaseTable {

    /**
     * Add a new category or update an existing one.
     *
     * Function adds a new category if category name does not exist. If it does exist and was deleted on todays date
     * the category is updated and its deletion_date set to null.
     *
     * @param  string $category_name name of the category.
     * @return boolean               returns true on query success and false if category already exists.
     * @throws Exception             if query fails.
     */
    public static function add_category($category_name, $date) {

        $sql = "SELECT * FROM CateringCategory
                WHERE name = '{$category_name}' AND deletion_date IS NULL";
        $result = parent::query($sql);
        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM CateringCategory
                    WHERE name = '{$category_name}' AND deletion_date = '{$date}'";
            $result = parent::query($sql);
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO CateringCategory (name, creation_date)
                        VALUES ('{$category_name}', '{$date}')";
            } else {
                $sql = "UPDATE CateringCategory SET deletion_date = NULL
                        WHERE name = '{$category_name}' and deletion_date = '{$date}'";
            }
            if (parent::query($sql)) {
                return true;
            } else {
                throw new Exception("add_category query failed");
            }
        } else {
            return false;
        }
    }

    /**
     * Delete a category in the database.
     *
     * Changes the category "deletion_date" to todays date. If successful, updates all items whos "category_id" matches the id
     * of the removed category and changes item "category_id"s to NULL.
     *
     * @param  int $category_id      id of the category to remove.
     * @return boolean               return true if query is succesful and false if it fails.
     */
    public static function remove_category($category_id, $date) {
        $sql = "UPDATE CateringCategory SET deletion_date = '$date'
                WHERE id = '$category_id' and deletion_date IS NULL";
        if (parent::query($sql)) {
            $sql = "UPDATE CateringItem SET category_id = NULL
                    WHERE deletion_date IS NULL AND category_id = '$category_id'";

            return parent::query($sql);
        }
    }

    /**
     * Get categories from the database.
     *
     * Returns all the categories that have a "deletion_date" of NULL or greater than todays date.
     *
     * @param  string $date       date till which categories will be retrieved.
     * @return object|false       returns mysqli_result object if data is retrieved or false if query fails.
     */
    public static function get_categories($date) {
        $sql = "SELECT * FROM CateringCategory
                WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' OR deletion_date IS NULL)
                ORDER BY order_id ASC";

        return parent::query($sql);
    }

    public static function update_category_name($name, $id) {
        $sql = "UPDATE CateringCategory
                SET name = '$name'
                WHERE id = $id";

        return parent::query($sql);
    }

    /**
     * Update order_id for the given category.
     *
     * @param  int  $category_id     id of the category to update.
     * @param  int  $order_id        new if to set for order_id.
     * @return boolean               return true if query is succesful and false if it fails.
     */
    public static function update_category_order($category_id, $order_id) {
        $sql = "UPDATE CateringCategory
                SET order_id = '$order_id'
                WHERE id = '$category_id'";

        return parent::query($sql);
    }
}
?>
