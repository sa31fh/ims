<?php 

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
     global $conn;
    connect_to_db();

    $role = $_POST["newRole"];
    $username = $_POST["newUserName"];

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

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    global $conn;
    connect_to_db();

    $date = $_POST["itemDate"];
    $item_id = $_POST["itemId"];
    $quantity = $_POST["itemQuantity"];
    $item_note = $_POST["itemNote"];

    $sql = "INSERT INTO Inventory (`date`, item_id, quantity, notes)
            VALUES ('$date', '$item_id', '$quantity', '$item_note')
            ON DUPLICATE KEY UPDATE 
            `date`= VALUES(`date`), item_id = VALUES(item_id), quantity = VALUES(quantity), notes = VALUES(notes)";

    if ($result = $conn->query($sql)) {
        return $result; 
    } else {
        echo "<br> update_inventory query failed <br>";
        return false;
    }
}

/*-----------------------category_status.php---------------*/
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

/*--------------edit_items.php------------*/
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

/*-----------------edit_categories.php-------------*/
if (isset($_POST["showCategorizedItems"])) {
    global $conn;
    connect_to_db();
    $category_name = $_POST["showCategorizedItems"];
        
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

/*----------------edit_categories.php----------------*/
if (isset($_POST["items"])) {
    global $conn;
    connect_to_db();

    $category_id = null;
    $category_name = $_POST["categoryName"];
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

/*-------------------------user_account.php----------------------*/ 
if (isset($_POST["timeZoneRegion"])) {

    $timezones = array( "Africa"=>"1", "America"=>"2", "Asia"=>"16", "Australia"=>"64", "Europe"=>"128");

    foreach (timezone_identifiers_list($timezones[$_POST["timeZoneRegion"]]) as $tz){
        $tzs = explode("/", $tz, 2);
        echo  '<option value="' .$tzs[1]. '">' .$tzs[1]. '</option>' ;
   }       
}


?>