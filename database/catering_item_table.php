<?php
require_once "database_table.php";

class CateringItemTable extends DatabaseTable {

    /**
     * Add a new item or update an existing one.
     *
     * Adds a new item if the item name does not exist. If it does exist and was deleted on todays date
     * the item is updated and its delete_date is set to null.
     *
     * @param   string  $item_name    Name of the item to add or update.
     * @param   int     $item_unit    Unit value of the item.
     * @return  boolean               Returns true on query success and false if item already exists.
     * @throws  exception             If query fails.
     */
    public static function add_new_item($item_name, $item_unit, $date, $price) {

        $sql = "SELECT * FROM CateringItems
                WHERE name = '{$item_name}'
                AND deletion_date IS NULL";

        $result = parent::query($sql);
        if ($result->num_rows != 0) {
            return false; // CateringItems already exists
        }
        $sql = "SELECT * FROM CateringItems
                WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
        $result = parent::query($sql);
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO CateringItems (name, unit, creation_date, price)
                    VALUES('{$item_name}', '{$item_unit}', '{$date}', $price)";
        } else {
            $sql = "UPDATE CateringItems
                    SET deletion_date = null, unit = '{$item_unit}', price = $price
                    WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
        }
        if (parent::query($sql)) {
            return true; // CateringItems added sucessfully
        } else {
            throw new Exception("add_new_item query failed!");
        }
    }

    /**
     * Get the total count of items present.
     *
     * @return  int         Returns count on query success.
     * @throws  exception   If query fails.
     */
    public static function get_items_count() {
        $sql = "SELECT COUNT(name) AS num FROM CateringItems
                LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = CateringItems.id
                WHERE CateringItems.deletion_date IS NULL
                ORDER BY name ASC";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['num'];
        } else {
            throw new Exception("get_items_count query failed");
        }
    }

    /**
     * Get items from database.
     *
     * @return object|false     Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_items($date) {
        $sql = "SELECT CateringItems.id, name, unit, BaseQuantity.quantity as base_quantity,
                Inventory.quantity AS quantity_stock, Inventory.quantity_delivered, Inventory.quantity_received,
                rounding_option, rounding_factor, price FROM CateringItems
                LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = CateringItems.id
                LEFT OUTER JOIN (SELECT item_id, quantity, quantity_delivered, quantity_received FROM Inventory
                                WHERE date = '$date') AS Inventory ON Inventory.item_id = CateringItems.id
                WHERE CateringItems.deletion_date IS NULL
                ORDER BY name ASC";

        return parent::query($sql);
    }

    /**
     * Gets all items within a specified range.
     *
     * @param  int  $offset      Start value of the item range.
     * @param  int  $limit       End value of the item range.
     * @return object|false      Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_items_in_range($offset, $limit) {
        $sql = "SELECT name, unit, quantity, id FROM CateringItems
                LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = ItCateringItemem.id
                WHERE CateringItems.deletion_date IS NULL
                ORDER BY name ASC
                LIMIT $offset, $limit";

        return parent::query($sql);
    }

    /**
     * Gets all items with their categories.
     *
     * @return object|false    Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_items_categories($date) {
        $sql = "SELECT CateringItems.name, unit, CateringItems.id, CateringItems.base_quantity, CateringItems.price, 
                CateringItems.rounding_factor, CateringItems.rounding_option, CateringCategory.name AS category_name, 
                CateringItems.order_id FROM CateringItems
                LEFT JOIN CateringCategory ON CateringItems.category_id = CateringCategory.id
                WHERE CateringItems.creation_date <= '{$date}' AND (CateringItems.deletion_date > '{$date}' OR CateringItems.deletion_date IS NULL)
                ORDER BY CateringCategory.order_id ASC, CateringItems.order_id ASC";

        return parent::query($sql);
    }

    /**
     * Delete an item in the database.
     *
     * Updates an items deletion_date to todays date for the given item name.
     *
     * @param  string   $item_name  Name of the item to be deleted.
     * @return boolean              Returns true on query success and false if it fails.
     */
    public static function delete_item($item_name) {
        $date = date('Y-m-d');

        $sql = "UPDATE CateringItems SET deletion_date = '{$date}'
                WHERE name = '{$item_name}'";

        return parent::query($sql);
    }

    /**
     * Delete multiple items.
     *
     * @param  array $item_ids   Id's of items to be deleted.
     * @return boolean           Returns true on query success and false if it fails.
     */
    public static function delete_multiple_items($item_ids, $date) {

        $sql = "UPDATE CateringItems SET deletion_date = '$date'
                WHERE id IN ('".implode("','", $item_ids)."')";

        return parent::query($sql);
    }

    /**
     * Update an item in the database.
     *
     * Updates item name and item unit for a give item id.
     *
     * @param  int      $item_id    Id of the item to be updated.
     * @param  string   $new_name   New name of the item.
     * @param  int      $new_unit   New unit value of the item.
     * @return boolean              Returns true on query success and false if it fails.
     */
    public static function update_item_details($item_id, $new_name, $new_unit, $price) {
        $sql = "UPDATE CateringItems
                SET name='$new_name',
                    unit='$new_unit',
                    price = '$price'
                WHERE id='$item_id'";

        return parent::query($sql);
    }

    public static function update_base_quantity($item_id, $quantity) {
        $sql = "UPDATE CateringItems
                SET base_quantity='$quantity'
                WHERE id='$item_id'";

        return parent::query($sql);
    }

    public static function set_base_quantity($item_name, $quantity) {
        $sql = "UPDATE CateringItems
                SET base_quantity='$quantity'
                WHERE name='$item_name'";

        return parent::query($sql);
    }

    public static function get_base_quantity($item_id) {
        $sql = "SELECT base_quantity FROM CateringItems
                WHERE id = '$item_id'";

        if($result = parent::query($sql)) {
            return $result->fetch_assoc()['base_quantity'];
        } else {
            throw new Exception("get_base_quantity query failed");
        }
    }


    public static function update_rounding_option($rounding_option, $item_id) {
        $sql = "UPDATE CateringItems
                SET rounding_option =  '$rounding_option'
                WHERE id = '$item_id'";

        return parent::query($sql);
    }

    public static function update_rounding_factor($rounding_factor, $item_id) {
        $sql = "UPDATE CateringItems
                SET rounding_factor = '$rounding_factor'
                WHERE id = '$item_id'";

        return parent::query($sql);
    }

    /**
     * Get total item count for items assigned to a given category till a given date.
     *
     * @param   int     $category_id    Id of the category whos item count is needed.
     * @param   string  $date           Date till which items will be counted.
     * @return  int                     Returns count value if query is successful
     * @throws  excetion                If query fails.
     */
    public static function get_total_items($category_id, $date) {
        $sql = "SELECT COUNT(CateringItems.name) AS num
                FROM CateringCategory INNER JOIN CateringItems
                ON CateringCategory.id = CateringItems.category_id
                WHERE CateringCategory.id = {$category_id}
                    AND (CateringCategory.creation_date <= '{$date}' AND (CateringCategory.deletion_date > '{$date}' OR CateringCategory.deletion_date IS NULL))
                    AND (CateringItems.creation_date <= '{$date}' AND (CateringItems.deletion_date > '{$date}' OR CateringItems.deletion_date IS NULL))";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['num'];
        } else {
            throw new Exception("get_total_items query failed");
        }
    }

    /**
     * Get total count of items that have been assigned a quantity for a given category till a given date.
     *
     * @param  int      $category_id    Id of the category whos item count is needed.
     * @param  string   $date           Date till which items will be counted.
     * @return int                      Returns count value if query is susccessful.
     * @throws exception                If query fails.
     */
    public static function get_updated_items_count($category_id, $date) {
        $sql = "SELECT COUNT(CateringItems.name) as num
                FROM CateringCategory INNER JOIN CateringItems ON CateringCategory.id = CateringItems.category_id
                LEFT JOIN Inventory ON CateringItems.id = Inventory.item_id
                WHERE CateringCategory.id = {$category_id} AND Inventory.date = '{$date}'
                    AND (CateringCategory.creation_date <= '{$date}' AND (CateringCategory.deletion_date > '{$date}' OR CateringCategory.deletion_date IS NULL))
                    AND (CateringItems.creation_date <= '{$date}' AND (CateringItems.deletion_date > '{$date}' OR CateringItems.deletion_date IS NULL))
                    AND Inventory.quantity IS NOT NULL";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['num'];
        } else {
            throw new Exception("get_updated_items_count query failed");
        }
    }

    /**
     * Get items assigned to a given category.
     *
     * @param  string   $category_name  Name of the category to get items for.
     * @return object|false             Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_categorized_items($category_name, $date) {
        $sql = "SELECT CateringItems.name, CateringItems.unit, CateringItems.id FROM CateringItems
                INNER JOIN CateringCategory ON CateringItems.category_id = CateringCategory.id
                WHERE CateringCategory.name = '{$category_name}'
                AND (CateringItems.creation_date <= '{$date}' AND (CateringItems.deletion_date > '{$date}' OR CateringItems.deletion_date IS NULL))
                ORDER BY CateringItems.order_id ASC";

       return parent::query($sql);
    }

    /**
     * Get items that have not been assigned to any category.
     *
     * @return object|false     Returns mysqli_result object on query success or false if query fails.
     */
    public static function get_uncategorized_items($date) {
        $sql = "SELECT name, unit, id FROM CateringItems
                WHERE category_id IS NULL
                AND (CateringItems.creation_date <= '{$date}' AND (CateringItems.deletion_date > '{$date}' OR CateringItems.deletion_date IS NULL))";

        return parent::query($sql);
    }

    public static function get_item_price($item_id) {
        $sql = "SELECT price FROM CateringItems
                WHERE id = '$item_id'";

        return parent::query($sql)->fetch_assoc()["price"];
    }

    /**
     * Update the category a given item is assigned to.
     *
     * If a category name is given the item is assigned to that name. Otherwise the items category_id is set to NULL.
     *
     * @param  string   $category_name  Name of the category to assign item to.
     * @param  string   $item_name      Name of the item to assign a category for.
     * @return boolean                  Returns true on query success and false if it fails.
     */
    public static function update_items_category($category_name, $item_name) {
        $category_id = null;

        if ($category_name != null) {
            $sql = "SELECT CateringCategory.id FROM CateringCategory
                    WHERE CateringCategory.name = '$category_name' AND deletion_date IS NULL";

            if ($result = parent::query($sql)) {
                $category_id = $result->fetch_assoc()['id'];
            }
        }
        $sql = "UPDATE CateringItems SET category_id =" .($category_id == null ? "null":$category_id). " WHERE name = '$item_name'";

        return parent::query($sql);
    }

    /**
     * Updates a given items list order value.
     *
     * @param  int  $category_id    Id of the item to update.
     * @param  int  $order_id       Order number to set for the item.
     * @return boolean              Returns true on query success and false if it fails.
     */
    public static function update_item_order($item_id, $order_id) {
        $sql = "UPDATE CateringItems
                SET order_id = '$order_id'
                WHERE id = '$item_id'";

        return parent::query($sql);
    }

    public static function get_estimated_quantity($factor, $item_id) {
        return round(self::get_base_quantity($item_id) * $factor, 2);
    }
}
?>
