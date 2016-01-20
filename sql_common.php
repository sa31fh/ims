<?php 
    $conn = null;

    function connect_to_db(){
        global $conn;

        $servername = "localhost";
        $username  = "root";
        $password = null;
        $dbname = "new_inventory";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if($conn->connect_error){
            die("Connection failed: " .$conn->connect_error);
        }
    }

    function add_new_user($username, $password, $userrole) {
        global $conn;
        connect_to_db();

        $sql = 'SELECT username FROM user  
                WHERE  user = "' .$username. '"';

        $result = $conn->query($sql);
        if ($result != false AND !null) {
            
            echo '<br>Error: Username already exists!<br>';
            return False;
        }

        $sql = "INSERT INTO User (username, password_hash, userrole_id) 
                VALUES('{$username}', '" .password_hash($password, PASSWORD_DEFAULT). "', 
                        (SELECT id FROM UserRole WHERE role='{$userrole}')) 
                ON DUPLICATE KEY UPDATE password_hash=VALUES(password_hash), userrole_id=VALUES(userrole_id)";

        $result = $conn->query($sql);

        if ($result == False) {
            echo "<br> Query failed <br>";
            return False; 
             }
        return True;    
    }

    function get_users(){
        global $conn;
        connect_to_db();

        $sql = 'SELECT * FROM User 
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            ORDER BY username ASC';

        $result = $conn->query($sql);

        if ($result == False) {
            echo "<br> Query failed <br>";
            return False; 
        }
        return $result;
    }

    function delete_user($username) {
        global $conn;
        connect_to_db();

        $sql = "DELETE FROM User WHERE username='$username'";

        $result = $conn->query($sql); 

        if ($result == False) {
            echo "<br> Delete Query failed <br>";
            return False;
        }
        return true;
    }

    function get_role(){
        global $conn;
        connect_to_db();

        $sql = "SELECT * FROM userrole";

        $result = $conn->query($sql);

        if($result == false){
            echo "Query failed";
            return false;
        }
        return $result;
    }

    function verify_credentials($username, $password) {
        global $conn;
        connect_to_db();

        $sql = "SELECT * FROM User
                INNER JOIN UserRole ON User.userrole_id = UserRole.id
                WHERE username='" .$username. "'";

        $result = $conn->query($sql);

        if ($result == False) {
            echo '<br> Query failed <br>';
            return false;
        }
        $row = $result->fetch_assoc();

        if ($row == null OR !password_verify($password, $row['password_hash']) ){
           return false;
        } else {
            $_SESSION["username"] = $username;
            $_SESSION['userrole'] = $row["role"];
            return true;
        }
    }

    function verify_password($username, $password) {
        global $conn;
        connect_to_db();

        $sql = "SELECT * FROM User
                INNER JOIN UserRole ON User.userrole_id = UserRole.id
                WHERE username='" .$username. "'";
        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br> Query failed <br>';
        }
        $row = $result->fetch_assoc();

        if ($row == null) {
            return False;
        }
        if (!password_verify($password, $row['password_hash'])) {
            return False;
        }
        return True;
    }

    if (isset($_POST["userName"])) {
        global $conn;
        connect_to_db();

        $username = $_POST["userName"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM User
                INNER JOIN UserRole ON User.userrole_id = UserRole.id
                WHERE username='" .$username. "'";
        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br> Query failed <br>';
        }
        $row = $result->fetch_assoc();

        if ($row == null) {
            return False;
        }
        if (!password_verify($password, $row['password_hash'])) {
            echo "false";
        } else {
        echo "true";}
    }

    function update_user_password($username, $new_password) {
        global $conn;
        connect_to_db();

        $sql = 'UPDATE User
                SET password_hash="' .password_hash($new_password, PASSWORD_DEFAULT). '" 
                WHERE username="' .$username. '"';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br> Query failed <br>';
            return false;
        }
        return true;
    }

    function get_categories($date) {
        global $conn;
        connect_to_db();

        if ($date == null) {
            $date = date('Y-m-d');
        }

        $sql = "SELECT * FROM Category 
                WHERE creation_date <= '{$date}' AND (deletion_date > '{$date}' OR deletion_date IS NULL) 
                ORDER BY name ASC";

        $result = $conn->query($sql); 

        if ($result == False) {
            echo "<br> Query failed <br>";
            return false;
        }
        return $result;
    }

    function add_category($category_name) {
        global $conn;
        connect_to_db();

        if ($category_name != null) {

            $sql = "SELECT * FROM Category 
                    WHERE name = '{$category_name}' AND deletion_date IS NULL";
            $result = $conn->query($sql);

            if ($result->num_rows == 0) {
                $date = date('Y-m-d');
                $sql = "SELECT * FROM Category 
                        WHERE name = '{$category_name}' AND deletion_date = '{$date}'";

                $result = $conn->query($sql); 
                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO Category (name, creation_date) 
                            VALUES ('{$category_name}', '{$date}')";
                } else {
                    $sql = "UPDATE Category SET deletion_date = NULL 
                            WHERE name = '{$category_name}' and deletion_date = '{$date}'";
                }

                $result = $conn->query($sql); 
                if ($result == False) {
                    echo "<br> Query failed <br>";
                    echo $sql;
                }
            } else {
                echo "Category already exists! </br>";
            }
        }
    }

    function remove_category($category_name) {
        global $conn;
        connect_to_db();

        $sql = "UPDATE Category SET deletion_date = '" .date('Y-m-d'). "' 
                WHERE name = '{$category_name}' and deletion_date IS NULL";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed </br>";
        }

        $sql = "UPDATE Item SET category_id = NULL  
                WHERE deletion_date IS NULL AND category_id = (SELECT id FROM Category WHERE name='{$category_name}')";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    function add_new_item($item_name, $item_unit) {
        global $conn;
        connect_to_db();

        $date = date('Y-m-d');

        $sql = "SELECT * FROM Item
                WHERE name = '{$item_name}' AND deletion_date IS NULL";

        $result = $conn->query($sql);
        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM Item 
                    WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
            $result = $conn->query($sql);
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO Item (name, unit, creation_date) 
                        VALUES('{$item_name}', '{$item_unit}', '{$date}')";
            } else {
                $sql = "UPDATE Item 
                        SET deletion_date = null, unit = '{$item_unit}' 
                        WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
            }
        } else {
            echo "Item already exists! <br/>";
        }
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    function get_items(){
        global $conn;
        connect_to_db();

        $sql = "SELECT name, unit, quantity, id FROM Item 
                LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = Item.id
                WHERE Item.deletion_date IS NULL 
                ORDER BY name ASC";

        $result = $conn->query($sql);
        if ($result == false) {
            echo "<br> Quert Failed </br>";
        }
        mysqli_close($conn);
        return $result;
    }

    function delete_item($item_name) {
        global $conn;
        connect_to_db();

        $date = date('Y-m-d');

        $sql = "UPDATE Item SET deletion_date = '{$date}' 
                WHERE name = '{$item_name}'";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
            echo $sql;
        }
    }

    function update_item_details($item_id, $new_name, $new_unit) {
        global $conn;
        connect_to_db();

        $sql = 'UPDATE Item 
                SET name="' .$new_name. '", 
                    unit="' .$new_unit. '"
                WHERE id="' .$item_id. '"';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    function get_total_items($category_id, $date) {
        global $conn;
        connect_to_db();

        $sql = "SELECT COUNT(Item.name) AS num
                from Category INNER JOIN Item 
                ON Category.id = Item.category_id
                WHERE Category.id = {$category_id} 
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))";
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }

        return $result->fetch_assoc()['num'];
    }

    function get_updated_items_count($category_id, $date) {
        global $conn;
        connect_to_db();

        $sql = "SELECT COUNT(Item.name) as num
                from Category INNER JOIN Item ON Category.id = Item.category_id 
                LEFT JOIN Inventory ON Item.id = Inventory.item_id 
                WHERE Category.id = {$category_id} AND Inventory.date = '{$date}' 
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        } else{
            return $result->fetch_assoc()['num'];
        }
    }

    function get_inventory($category_id, $date) {
        global $conn;
        connect_to_db();

        $sql = "SELECT T2.item_id AS id, T2.item_name AS name, T2.item_unit AS unit, IFNULL(T1.quantity, \"-\") AS quantity, T1.notes AS notes FROM
                (SELECT * from Inventory
                WHERE Inventory.date = '{$date}') AS T1
                RIGHT JOIN
                (SELECT Item.id AS item_id, Item.name AS item_name, Item.unit AS item_unit from Item
                INNER JOIN Category ON Item.category_id = Category.id
                WHERE Category.id = {$category_id} 
                    AND (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                    AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL))) AS T2 ON T2.item_id = T1.item_id 
                ORDER BY T2.item_name";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
        return $result;
    }

    function update_inventory($category_id, $date, $items, $values, $notes) {
        global $conn;
        connect_to_db();

        for ($i = 0; $i < count($items); $i++) {
            if ($values[$i] == null) {
                continue;
            }

            $sql = 'INSERT INTO Inventory (`date`, `item_id`, `quantity`, `notes`)
                    VALUES ("' .$date. '", ' .$items[$i]. ', ' .$values[$i]. ', "' .$notes[$i]. '")
                    ON DUPLICATE KEY UPDATE 
                    date=VALUES(date), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)';

            $result = $conn->query($sql); 
            if ($result == False) {
                echo "<br> Query failed <br>";
            }
        }
    }

    if (isset($_POST["itQuan"])) {
        global $conn;
        connect_to_db();

        $date = $_POST["itDate"];
        $item_id = $_POST["itId"];
        $quantity = $_POST["itQuan"];
        $item_note = $_POST["itNote"];

        $sql = 'INSERT INTO Inventory (`date`, `item_id`, `quantity`, `notes`)
                VALUES ("' .$date. '", ' .$item_id. ', ' .$quantity. ', "' .$item_note. '")
                ON DUPLICATE KEY UPDATE 
                date=VALUES(date), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)';

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    function get_expected_sales() {
        global $conn;
        connect_to_db();

        $sql = 'SELECT value FROM Variables WHERE name="ExpectedSales"';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }

        return (int) $result->fetch_assoc()['value'];
    }

    function update_expected_sales($expected_sales) {
        global $conn;
        connect_to_db();
        
        $sql = 'INSERT INTO Variables (name, value)  
                VALUES ("ExpectedSales", ' .$expected_sales. ') 
                ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    if (isset($_POST["sales"])) {
        global $conn;
        connect_to_db();

        $expected_sales = $_POST["sales"];
        
        $sql = 'INSERT INTO Variables (name, value)  
                VALUES ("ExpectedSales", ' .$expected_sales. ') 
                ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    function get_base_quantity($item_name) {
        global $conn;
        connect_to_db();

        $sql = 'SELECT quantity FROM BaseQuantity  
                WHERE item_id = (SELECT id FROM Item WHERE name="' .$item_name. '")';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>get_base_quantity() Query Failed<br>';
            echo $sql;
        }
        return (int) $result->fetch_assoc()['quantity'];
    }

    function update_base_quantity($item_id, $quantity) {
        global $conn;
        connect_to_db();

        $sql = 'INSERT INTO BaseQuantity (item_id, quantity)  
                VALUES (' .$item_id.' , ' .$quantity. ') 
                ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    if (isset($_POST["quantity"])) {
        global $conn;
        connect_to_db();

        $quantity = $_POST["quantity"];
        $item_id = $_POST["item_id"];

        $sql = 'INSERT INTO BaseQuantity (item_id, quantity)  
                VALUES (' .$item_id.' , ' .$quantity. ') 
                ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    function get_base_sales() {
        global $conn;
        connect_to_db();

        $sql = 'SELECT value FROM Variables WHERE name="BaseSales"';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
        return (int) $result->fetch_assoc()['value'];
    }

    function update_base_sales($base_sales) {
        global $conn;
        connect_to_db();
        
        $sql = 'INSERT INTO Variables (name, value)  
                VALUES ("BaseSales", ' .$base_sales. ') 
                ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)';

        $result = $conn->query($sql);
        if ($result == False) {
            echo '<br>Query Failed<br>';
        }
    }

    function get_estimated_quantity($factor, $item_name) {
       
        return round(get_base_quantity($item_name) * $factor, 2);
    }

    function get_categorized_items($category_name){
        global $conn;
        connect_to_db();
            
        $sql = "SELECT Item.name, Item.unit FROM Item 
                INNER JOIN Category ON Item.category_id = Category.id 
                WHERE Category.name = '{$category_name}' AND Item.deletion_date IS NULL";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
        return $result;
    }

    if (isset($_POST["categoryName"])) {
        global $conn;
        connect_to_db();
        $category_name = $_POST["categoryName"];
            
        $sql = "SELECT Item.name, Item.unit FROM Item 
                INNER JOIN Category ON Item.category_id = Category.id 
                WHERE Category.name = '{$category_name}' AND Item.deletion_date IS NULL";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
        echo '<select id="categorized_list" size=8>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' .$row["name"]. '">' .$row["name"]. ' </option>';
        }
        echo '</select>';
    }

    function get_uncategorized_items(){
        global $conn;
        connect_to_db();

        $sql = 'SELECT name, unit FROM Item WHERE category_id IS NULL AND deletion_date IS NULL';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
        return $result;
    }

    function update_items_category($category_name, $items) {
        global $conn;
        connect_to_db();

        $category_id = null;

        if ($category_name != null) {
            $sql = 'SELECT Category.id FROM Category 
                    WHERE Category.name = "' .$category_name. '" AND deletion_date IS NULL';

            $result = $conn->query($sql); 
            if ($result == False) {
                echo "<br> Query failed <br>";
            }
            $category_id = $result->fetch_assoc()['id'];
        }
        $sql = 'UPDATE Item SET category_id = ' .($category_id == null ? "null":$category_id). ' WHERE name = "' .$items. '"';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }
    
    if (isset($_POST["items"])) {
        global $conn;
        connect_to_db();

        $category_id = null;
        $category_name = $_POST["catnam"];
        $items = $_POST["items"];

        if ($category_name != null) {
            $sql = 'SELECT Category.id FROM Category 
                    WHERE Category.name = "' .$category_name. '" AND deletion_date IS NULL';

            $result = $conn->query($sql); 
            if ($result == False) {
                echo "<br> Query failed <br>";
            }
            $category_id = $result->fetch_assoc()['id'];
        }
        $sql = 'UPDATE Item SET category_id = ' .($category_id == null ? "null":$category_id). ' WHERE name = "' .$items. '"';
        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
    }

    function categorize_items($category_name, $item_name){
        global $conn;
        connect_to_db();

         $sql = 'SELECT id FROM Category 
                 WHERE name = "' .$category_name. '" AND deletion_date IS NULL';

          $result = $conn->query($sql);
          $category_id = $result->fetch_assoc()['id'];

        $sql = 'UPDATE item SET category_id = "' .$category_id. '" WHERE name = "' .$item_name.'"';

        $result = $conn->query($sql);
        if ($result == False) {
                echo "<br> Query failed <br>";
        }
    }

    function print_preview(){
        global $conn;
        connect_to_db();

        $date = date('Y-m-d');

        $sql = "SELECT Category.name as category_name, Item.name as item_name, 
                IFNULL(unit, '-') as unit, IFNULL(quantity, '-') as quantity, Inv.notes as notes 
            FROM Category
            INNER JOIN Item ON Item.category_id = Category.id
            LEFT OUTER JOIN (SELECT * FROM Inventory WHERE date='{$date}') AS Inv ON Inv.item_id = Item.id 
            WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
                AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL)) 
            ORDER BY Category.name, Item.name";

        $result = $conn->query($sql); 
        if ($result == False) {
            echo "<br> Query failed <br>";
        }
        return $result;
    }
?>