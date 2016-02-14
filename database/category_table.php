<?php 
require_once "data_base_table.php";

class CategoryTable extends DatabaseTable {

	public static function add_category($category_name) {

		$sql = "SELECT * FROM Category 
            	WHERE name = '{$category_name}' AND deletion_date IS NULL";

        if ($result = parent::query($sql)){

	        if ($result->num_rows == 0) {
	            $date = date('Y-m-d');
	            $sql = "SELECT * FROM Category 
	                    WHERE name = '{$category_name}' AND deletion_date = '{$date}'";

	            $result = parent::query($sql); 
	            if ($result->num_rows == 0) {
	                $sql = "INSERT INTO Category (name, creation_date) 
	                        VALUES ('{$category_name}', '{$date}')";
	            } 
	            else {
	                $sql = "UPDATE Category SET deletion_date = NULL 
	                        WHERE name = '{$category_name}' and deletion_date = '{$date}'";
	            }

	            if($result = parent::query($sql)){ 
	                return true;
	            } else {
	                echo "Query Failed";
	                return false;
	            }
	        } else {
	            echo "Category already exists! </br>";
	        }
	    } else {
	        echo "add_category query failed";
	        return false;
	    }   	
	}

	public static function remove_category($category_name) {

		$sql = "UPDATE Category SET deletion_date = '" .date('Y-m-d'). "' 
          		WHERE name = '{$category_name}' and deletion_date IS NULL";

        if ($result = parent::query($sql)) {
	        $sql = "UPDATE Item SET category_id = NULL  
	                WHERE deletion_date IS NULL AND category_id = (SELECT id FROM Category WHERE name='{$category_name}')";

	        $result = parent::query($sql);
	    } else {
	        echo "remove_category query failed";
	    }
	}

	public static function get_categories($date) {

		$sql = "SELECT * FROM Category 
	            WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' OR deletion_date IS NULL) 
	            ORDER BY name ASC";

        $result = parent::query($sql);
        return $result;
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

        $result = parent::query($sql);
        return $result;
	}
}

?>