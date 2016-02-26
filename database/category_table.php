<?php 
require_once "database_table.php";

class CategoryTable extends DatabaseTable {

    public static function add_category($category_name) {
        $date = date('Y-m-d');
        
        $sql = "SELECT * FROM Category 
                WHERE name = '{$category_name}' AND deletion_date IS NULL";
        $result = parent::query($sql);
        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM Category 
                    WHERE name = '{$category_name}' AND deletion_date = '{$date}'";
            $result = parent::query($sql); 
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO Category (name, creation_date) 
                        VALUES ('{$category_name}', '{$date}')";
            } else {
                $sql = "UPDATE Category SET deletion_date = NULL 
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


    public static function remove_category($category_name) {
        $sql = "UPDATE Category SET deletion_date = '" .date('Y-m-d'). "' 
                WHERE name = '{$category_name}' and deletion_date IS NULL";
        if ($result = parent::query($sql)) {
            $sql = "UPDATE Item SET category_id = NULL  
                    WHERE deletion_date IS NULL AND category_id = (SELECT id FROM Category WHERE name='{$category_name}')";
                    
            return parent::query($sql);
        }
    }

    public static function get_categories($date) {
        $sql = "SELECT * FROM Category 
                WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' OR deletion_date IS NULL) 
                ORDER BY name ASC";

        return parent::query($sql);
    }

    public static function get_print_preview($date) {
        $sql = "SELECT Category.name as category_name, Item.name as item_name, 
                    IFNULL(unit, '-') as unit, IFNULL(quantity, '-') as quantity, Inv.notes as notes 
                FROM Category
                INNER JOIN Item ON Item.category_id = Category.id
                LEFT OUTER JOIN (SELECT * FROM Inventory WHERE date='{$date}') AS Inv ON Inv.item_id = Item.id 
                WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL)) 
                ORDER BY Category.name, Item.name";

        return parent::query($sql);
    }
}
?>
