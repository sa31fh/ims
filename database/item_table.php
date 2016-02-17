<?php 
require_once "database_table.php";

class ItemTable extends DatabaseTable {

    public static function add_new_item($item_name, $item_unit) {
        $date = date('Y-m-d');

        $sql = "SELECT * FROM Item
                WHERE name = '{$item_name}' 
                AND deletion_date IS NULL";

        if($result = parent::query($sql)) {
            if ($result->num_rows == 0) {
                $sql = "SELECT * FROM Item 
                        WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
                if($result = parent::query($sql)) {
                    if ($result->num_rows == 0) {
                        $sql = "INSERT INTO Item (name, unit, creation_date) 
                                VALUES('{$item_name}', '{$item_unit}', '{$date}')";
                    } else {
                        $sql = "UPDATE Item 
                                SET deletion_date = null, unit = '{$item_unit}' 
                                WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
                    }
                    if ($result = parent::query($sql)) {
                        return $result;
                    }
                }
            } else {
                throw new Exception("Item already exists!");
            }
        }
    }

    public static function get_items() {
        $sql = "SELECT name, unit, quantity, id FROM Item 
                LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = Item.id
                WHERE Item.deletion_date IS NULL 
                ORDER BY name ASC";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }

    public static function delete_item($item_name) {
        $date = date('Y-m-d');

        $sql = "UPDATE Item SET deletion_date = '{$date}' 
                WHERE name = '{$item_name}'";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }

    public static function update_item_details($item_id, $new_name, $new_unit) {
        $sql = "UPDATE Item 
                SET name='$new_name', 
                    unit='$new_unit'
                WHERE id='$item_id'";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }

    public static function get_total_items($category_id, $date) {
        $sql = "SELECT COUNT(Item.name) AS num
                from Category INNER JOIN Item 
                ON Category.id = Item.category_id
                WHERE Category.id = {$category_id} 
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))";
                    
        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['num'];
        }
    }

    public static function get_updated_items_count($category_id, $date) {
        $sql = "SELECT COUNT(Item.name) as num
                from Category INNER JOIN Item ON Category.id = Item.category_id 
                LEFT JOIN Inventory ON Item.id = Inventory.item_id 
                WHERE Category.id = {$category_id} AND Inventory.date = '{$date}' 
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))";

        if ($result = parent::query($sql)) {
            return $result->fetch_assoc()['num'];
        }
    }

    public static function get_categorized_items($category_name) {
        $sql = "SELECT Item.name, Item.unit FROM Item 
                INNER JOIN Category ON Item.category_id = Category.id 
                WHERE Category.name = '{$category_name}' AND Item.deletion_date IS NULL";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }

    public static function get_uncategorized_items() {
        $sql = "SELECT name, unit FROM Item WHERE category_id IS NULL AND deletion_date IS NULL";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }

    public static function update_items_category($category_name, $item_name) {
        $category_id = null;

        if ($category_name != null) {
            $sql = "SELECT Category.id FROM Category 
                    WHERE Category.name = '$category_name' AND deletion_date IS NULL";

            if ($result = parent::query($sql)) {
                $category_id = $result->fetch_assoc()['id'];
            }
        }
        $sql = "UPDATE Item SET category_id =" .($category_id == null ? "null":$category_id). " WHERE name = '$item_name'";

        if ($result = parent::query($sql)) {
            return $result;
        }
    }
}

 ?>