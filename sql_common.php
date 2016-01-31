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

    $sql = "SELECT username FROM User  
            WHERE  username = '$username'";

    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row["username"] == $username) {
        echo '<br>Error: Username already exists!<br>';
        return False;
    }

    $sql = "INSERT INTO User (username, password_hash, userrole_id) 
            VALUES('{$username}', '" .password_hash($password, PASSWORD_DEFAULT). "', 
                    (SELECT id FROM UserRole WHERE role='{$userrole}'))";

    if ($result = $conn->query($sql)) {
        return True; 
    } else {
        echo "<br> add_new_user query failed <br>";
        return False; 
    }

}

function get_users(){
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM User 
        INNER JOIN UserRole ON User.userrole_id = UserRole.id
        ORDER BY username ASC";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_users query failed <br>";
        return false;
    }
}

function get_user_details($user){
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM User 
        INNER JOIN UserRole ON User.userrole_id = UserRole.id
        WHERE username = '$user'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_user_details query failed <br>";
        return false;
    }
}
function update_user_details($user, $first_name, $last_name, $time_zone){
    global $conn;
    connect_to_db();

    $sql = "UPDATE User
            SET username = '$user', 
                first_name = '$first_name',
                last_name = '$last_name',
                time_zone = '$time_zone'
            WHERE username = '$user'";

    if ($result = $conn->query($sql)) {
        return true;
    } else {
        echo "<br> update_user Query failed <br>";
        return false;
    }
}

function delete_user($username) {
    global $conn;
    connect_to_db();

    $sql = "DELETE FROM User WHERE username='$username'";

    $result = $conn->query($sql); 

    if ($result = $conn->query($sql)) {
        return true; 
    } else {
        echo "<br> delete_user query failed <br>";
        return false;
    }
}

function get_role(){
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM UserRole";

    $result = $conn->query($sql);

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_user_role query failed <br>";
        return false;
    }
}

function update_user_role($username, $role) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE User 
            SET userrole_id= (SELECT id FROM UserRole WHERE role='$role') 
            WHERE username='$username'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br update_user_role query failed <br>";
        return false;
    }
}
/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
     global $conn;
    connect_to_db();

    $role = $_POST["newRole"];
    $username = $_POST["UserName"];

    $sql = "UPDATE User 
            SET userrole_id= (SELECT id FROM UserRole WHERE role='$role') 
            WHERE username='$username'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_user_role query failed <br>";
        return false;
    }
}

function verify_credentials($username, $password) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM User
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            WHERE username='$username'";

    if ($result = $conn->query($sql)) {
        
        $row = $result->fetch_assoc();

        if ($row == null OR !password_verify($password, $row['password_hash']) ){
           return false;
        } else {
            return true;
        }
    } else {
        echo "Verify Credentials Query Failed";
        return false;
    }
}

function set_session_variables($username){
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM User
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            WHERE username='$username'";

    if ($result = $conn->query($sql)) {
        
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $username;
        $_SESSION["userrole"] = $row["role"];
        if (!empty($row["time_zone"])) {
            $_SESSION["timezone"] = $row["time_zone"];
        } else {
            $_SESSION["timezone"] = date_default_timezone_get();
        }
    } else {
        echo "set_session_variables query failed";
    }
}

/*---------user_account.php--------------*/
if (isset($_POST["userName"])) {
    global $conn;
    connect_to_db();

    $username = $_POST["userName"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM User
            INNER JOIN UserRole ON User.userrole_id = UserRole.id
            WHERE username='$username'";
    
    if ($result = $conn->query($sql)) {
        $row = $result->fetch_assoc();

        if ($row == null) {
            return False;
        }
        if (!password_verify($password, $row['password_hash'])) {
            echo "false";
        } else {
            echo "true";
        }
    } else {
        echo "Verify Password Query Failed";
        return false;
    }
}

function update_user_password($username, $new_password) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE User
            SET password_hash='" .password_hash($new_password, PASSWORD_DEFAULT). "' 
            WHERE username='$username'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> Update User Password query failed <br>";
        return false;
    }
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

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_categories query failed <br>";
        return false;
    }
}

function add_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Category 
            WHERE name = '{$category_name}' AND deletion_date IS NULL";

    if ($result = $conn->query($sql)){

        if ($result->num_rows == 0) {
            $date = date('Y-m-d');
            $sql = "SELECT * FROM Category 
                    WHERE name = '{$category_name}' AND deletion_date = '{$date}'";

            $result = $conn->query($sql); 
            if ($result->num_rows == 0) {
                $sql = "INSERT INTO Category (name, creation_date) 
                        VALUES ('{$category_name}', '{$date}')";
            } 
            else {
                $sql = "UPDATE Category SET deletion_date = NULL 
                        WHERE name = '{$category_name}' and deletion_date = '{$date}'";
            }

            if($result = $conn->query($sql)){ 
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


function remove_category($category_name) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE Category SET deletion_date = '" .date('Y-m-d'). "' 
            WHERE name = '{$category_name}' and deletion_date IS NULL";

    if ($result = $conn->query($sql)) {
        $sql = "UPDATE Item SET category_id = NULL  
                WHERE deletion_date IS NULL AND category_id = (SELECT id FROM Category WHERE name='{$category_name}')";

        $result = $conn->query($sql);
    } else {
        echo "remove_category query failed";
    }
}

function add_new_item($item_name, $item_unit) {
    global $conn;
    connect_to_db();

    $date = date('Y-m-d');

    $sql = "SELECT * FROM Item
            WHERE name = '{$item_name}' AND deletion_date IS NULL";

    if($result = $conn->query($sql)){
        if ($result->num_rows == 0) {
            $sql = "SELECT * FROM Item 
                    WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
            if($result = $conn->query($sql)){
                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO Item (name, unit, creation_date) 
                            VALUES('{$item_name}', '{$item_unit}', '{$date}')";
                } else {
                    $sql = "UPDATE Item 
                            SET deletion_date = null, unit = '{$item_unit}' 
                            WHERE name = '{$item_name}' AND deletion_date = '{$date}'";
                }
                if(!($result = $conn->query($sql))){
                    echo "add_new_item query failed";
                }
            }
        } else {
            echo "Item already exists! <br/>";
        }
    } else{
        echo "add_new_item query failed";
    }
}

function get_items(){
    global $conn;
    connect_to_db();

    $sql = "SELECT name, unit, quantity, id FROM Item 
            LEFT OUTER JOIN BaseQuantity ON BaseQuantity.item_id = Item.id
            WHERE Item.deletion_date IS NULL 
            ORDER BY name ASC";

    if($result = $conn->query($sql)){
        mysqli_close($conn);
        return $result;
    } else {
        echo "get_items query failed";
    }
    
}

function delete_item($item_name) {
    global $conn;
    connect_to_db();

    $date = date('Y-m-d');

    $sql = "UPDATE Item SET deletion_date = '{$date}' 
            WHERE name = '{$item_name}'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> delete_item query failed <br>";
        return false;
    }
}

function update_item_details($item_id, $new_name, $new_unit) {
    global $conn;
    connect_to_db();

    $sql = "UPDATE Item 
            SET name='$new_name', 
                unit='$new_unit'
            WHERE id='$item_id'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_item_details query failed <br>";
        return false;
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
   
   if ($result = $conn->query($sql)) {
        return $result->fetch_assoc()['num'];
    } else {
        echo "<br> get_total_items query failed <br>";
        return false;
    }

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

    if ($result = $conn->query($sql)) {
        return $result->fetch_assoc()['num'];
    } else{
        echo "<br> get_updated_items_count Query failed <br>";
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

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_inventory query failed <br>";
        return false;
    }
}

function update_inventory($category_id, $date, $items, $values, $notes) {
    global $conn;
    connect_to_db();

    for ($i = 0; $i < count($items); $i++) {
        if ($values[$i] == null) {
            continue;
        }

        $sql = "INSERT INTO Inventory (`date`, `item_id`, `quantity`, `notes`)
                VALUES ('" .$date. "', '" .$items[$i]. "', '" .$values[$i]. "', '" .$notes[$i]. "')
                ON DUPLICATE KEY UPDATE 
                date=VALUES(date), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)";

        if ($result = $conn->query($sql)) {
            return $result; 
        } else {
            echo "<br> update_inventory query failed <br>";
            return false;
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

    $sql = "INSERT INTO Inventory (`date`, item_id, quantity, notes)
            VALUES ('$date', '$item_id', '$quantity', '$item_note')
            ON DUPLICATE KEY UPDATE 
            date=VALUES(date), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_inventory query failed <br>";
        return false;
    }
}

function get_expected_sales() {
    global $conn;
    connect_to_db();

    $sql = "SELECT value FROM Variables WHERE name='ExpectedSales'";

    if ($result = $conn->query($sql)) {
        return (int) $result->fetch_assoc()['value'];
    } else {
        echo "<br> get_expected_sales query failed <br>";
        return false;
    }
}

function update_expected_sales($expected_sales) {
    global $conn;
    connect_to_db();
    
    $sql = "INSERT INTO Variables (name, value)  
            VALUES ('ExpectedSales', '$expected_sales') 
            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_expected_sales query failed <br>";
        return false;
    }
}

if (isset($_POST["sales"])) {
    global $conn;
    connect_to_db();

    $expected_sales = $_POST["sales"];
    
    $sql = "INSERT INTO Variables (name, value)  
            VALUES ('ExpectedSales', '$expected_sales') 
            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_expected_sales query failed <br>";
        return false;
    }
}

function get_base_quantity($item_name) {
    global $conn;
    connect_to_db();

    $sql = "SELECT quantity FROM BaseQuantity  
            WHERE item_id = (SELECT id FROM Item WHERE name='$item_name')";

    if ($result = $conn->query($sql)) {
        return (int) $result->fetch_assoc()['quantity'];
    } else {
        echo "<br> get_base_quantity query failed <br>";
        return false;
    }
}

function update_base_quantity($item_id, $quantity) {
    global $conn;
    connect_to_db();

    $sql = "INSERT INTO BaseQuantity (item_id, quantity)  
            VALUES ('$item_id' , '$quantity') 
            ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_base_quantity query failed <br>";
        return false;
    }
}

if (isset($_POST["quantity"])) {
    global $conn;
    connect_to_db();

    $quantity = $_POST["quantity"];
    $item_id = $_POST["item_id"];

    $sql = "INSERT INTO BaseQuantity (item_id, quantity)  
            VALUES ('$item_id' , '$quantity') 
            ON DUPLICATE KEY UPDATE item_id = VALUES(item_id), quantity = VALUES(quantity)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_base_quantity query failed <br>";
        return false;
    }
}

function get_base_sales() {
    global $conn;
    connect_to_db();

    $sql = "SELECT value FROM Variables WHERE name='BaseSales'";

    if ($result = $conn->query($sql)) {
        return (int) $result->fetch_assoc()['value'];
    } else {
        echo "<br> get_base_sales query failed <br>";
        return false;
    }
}

function update_base_sales($base_sales) {
    global $conn;
    connect_to_db();
    
    $sql = "INSERT INTO Variables (name, value)  
            VALUES ('BaseSales', '$base_sales') 
            ON DUPLICATE KEY UPDATE name = VALUES(name), value = VALUES(value)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_base_sales query failed <br>";
        return false;
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

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_categorized_items query failed <br>";
        return false;
    }
}

if (isset($_POST["categoryName"])) {
    global $conn;
    connect_to_db();
    $category_name = $_POST["categoryName"];
        
    $sql = "SELECT Item.name, Item.unit FROM Item 
            INNER JOIN Category ON Item.category_id = Category.id 
            WHERE Category.name = '{$category_name}' AND Item.deletion_date IS NULL";

    if ($result = $conn->query($sql)) {
        echo '<select class="category_select" id="categorized_list" size=8>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' .$row["name"]. '">' .$row["name"]. ' </option>';
        }
        echo '</select>';
    } else {
        echo "<br> get_categorized_items query failed <br>";
        return false;
    }
}

function get_uncategorized_items(){
    global $conn;
    connect_to_db();

    $sql = "SELECT name, unit FROM Item WHERE category_id IS NULL AND deletion_date IS NULL";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_uncategorized_items query failed <br>";
        return false;
    }
}

function update_items_category($category_name, $items) {
    global $conn;
    connect_to_db();

    $category_id = null;

    if ($category_name != null) {
        $sql = "SELECT Category.id FROM Category 
                WHERE Category.name = '$category_name.' AND deletion_date IS NULL";

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
    $category_name = $_POST["CategoryName"];
    $items = $_POST["items"];

    if ($category_name != null) {
        $sql = "SELECT Category.id FROM Category 
                WHERE Category.name = '$category_name' AND deletion_date IS NULL";

        if ($result = $conn->query($sql)) {
            $category_id = $result->fetch_assoc()['id'];
        }
    }
    $sql = "UPDATE Item SET category_id =" .($category_id == null ? "null":$category_id). " WHERE name = '$items'";
    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_items_category query failed <br>";
        return false;
    }
}

function categorize_items($category_name, $item_name) {
    global $conn;
    connect_to_db();

    $sql = "SELECT id FROM Category 
            WHERE name = '$category_name' AND deletion_date IS NULL";

    if ($result = $conn->query($sql)) {
        $category_id = $result->fetch_assoc()['id'];
        $sql = "UPDATE item SET category_id = '$category_id' WHERE name = '$item_name'";
       if ($result = $conn->query($sql)) {
            return $result; 
        } else {
            echo "<br> categorize_items query failed <br>";
            return false;
        }
    }

}

function get_print_preview($date) {
    global $conn;
    connect_to_db();

    $sql = "SELECT Category.name as category_name, Item.name as item_name, 
            IFNULL(unit, '-') as unit, IFNULL(quantity, '-') as quantity, Inv.notes as notes 
        FROM Category
        INNER JOIN Item ON Item.category_id = Category.id
        LEFT OUTER JOIN (SELECT * FROM Inventory WHERE date='{$date}') AS Inv ON Inv.item_id = Item.id 
        WHERE (Category.creation_date <= '{$date}' AND (Category.deletion_date > '{$date}' OR Category.deletion_date IS NULL)) 
            AND (Item.creation_date <= '{$date}' AND (Item.deletion_date > '{$date}' OR Item.deletion_date IS NULL)) 
        ORDER BY Category.name, Item.name";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_print_preview query failed <br>";
        return false;
    }
}

function get_sent_conversations($user) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Conversation
            WHERE sender = '$user'
            ORDER BY `timestamp` DESC";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_sent_conversations query failed <br>";
        return false;
    }
}

function get_received_conversations($user) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Conversation
            WHERE (sender = '$user' AND sender_delete = false )
            OR (receiver = '$user'AND receiver_delete = false)
            ORDER BY `timestamp` DESC";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> received_conversations query failed <br>";
        return false;
    }
}

function get_deleted_conversations($user) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Conversation
            WHERE (sender = '$user' AND sender_delete = true )
            OR (receiver = '$user' AND receiver_delete = true)
            ORDER BY `timestamp` DESC";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_deleted_conversations query failed <br>";
        return false;
    }    
}

function set_new_conversation($sender_name, $receiver_name, $title, $message, $date, $attachment) {
    global $conn;
    connect_to_db();

    $sql = "INSERT INTO Conversation (`timestamp`, sender, receiver, title, sender_delete, receiver_delete)
            VALUES ('$date' , '$sender_name' , '$receiver_name' , '$title', 'false', 'false')";

    if ($result = $conn->query($sql)) {
        $sql = "SELECT id from Conversation ORDER BY id DESC LIMIT 1";
            if($result = $conn->query($sql)){
                $id = (int) $result->fetch_assoc()['id'];

                $sql = "INSERT INTO Message (`timestamp`, sender, receiver, message, attachment, conversation_id)
                        VALUES ('$date', '$sender_name', '$receiver_name', '$message', '$attachment', '$id')";

                if ($result = $conn->query($sql)) {
                    return $result; 
                } else {
                    echo "<br> set_new_conversation insert into message query failed <br>";
                    return false;
                }
            }
    } else {
        echo "<br> set_new_conversation query failed <br>";
        return false;
    }
}

function delete_conversation($conversation_id) {
    global $conn;
    connect_to_db();

    $sql = "SELECT sender FROM Conversation
            WHERE id = '$conversation_id'";

    if($result = $conn->query($sql)){
        $row = $result->fetch_assoc();
        if ($row["sender"] == $_SESSION["username"]) {
            $deletefrom = "sender_delete";
        } else {
            $deletefrom = "receiver_delete";
        }

        $sql = "UPDATE Conversation 
                SET " .$deletefrom. " = true
                WHERE id = '$conversation_id'";

        if($result = $conn->query($sql)){
            return true;
        } else {
            echo "delete_conversation query failed";
        }
    } else {
            echo "delete_conversation query failed";
    }
}

function get_messages($conversation_id) {
    global $conn;
    connect_to_db();

    $sql = "SELECT * FROM Message
            WHERE conversation_id = '$conversation_id'";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> get_messages query failed <br>";
        return false;
    }
}

function set_new_message($sender, $receiver, $message, $conversation_id, $date) {
    global $conn;
    connect_to_db();

    $sql = "INSERT INTO Message (`timestamp`, sender, receiver, message, conversation_id)
            VALUES ('$date', '$sender', '$receiver', '$message', '$conversation_id')";

    if ($result = $conn->query($sql)) {
        $sql = "UPDATE Conversation 
                SET `timestamp`='$date'
                WHERE id = '$conversation_id'";

        if($result = $conn->query($sql)){
            return true;
        } else {
            echo "set_new_message query failed";
            return false;
        }
    } else {
        echo "<br> set_new_message query failed <br>";
        return false;
    }

}

if (isset($_POST["timeZoneRegion"])) {

    $timezones = array( "Africa"=>"1", "America"=>"2", "Asia"=>"16", "Australia"=>"64", "Europe"=>"128");

    foreach (timezone_identifiers_list($timezones[$_POST["timeZoneRegion"]]) as $tz){
        $tzs = explode("/", $tz, 2);
        echo  '<option value="' .$tzs[1]. '">' .$tzs[1]. '</option>' ;
   }       
}

function convert_date_timezone($date) {

    $newDate = date_create($date, timezone_open('GMT'));
    $tz_date = date_timezone_set($newDate, timezone_open($_SESSION["timezone"]));
    return date_format($tz_date, "h:ia d/m/Y");
}
?>
