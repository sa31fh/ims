<?php 
include "sql_common.php";

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
    update_user_role($_POST["roleUserName"], $_POST["newRole"]);
}

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    update_inventory($_POST["itemDate"], $_POST["itemId"], $_POST["itemQuantity"], $_POST["itemNote"]);
}

/*-----------------------category_status.php---------------*/
if (isset($_POST["sales"])) {
    update_expected_sales($_POST["sales"]);
}

/*--------------edit_items.php------------*/
if (isset($_POST["quantity"])) {
    update_base_quantity($_POST["itemId"], $_POST["quantity"]);
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
    update_items_category($_POST["categoryName"], $_POST["items"]);
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

/*-------------------------user_account.php----------------------*/ 
if (isset($_POST["timeZoneRegion"])) {

    $timezones = array( "Africa"=>"1", "America"=>"2", "Asia"=>"16", "Australia"=>"64", "Europe"=>"128");

    foreach (timezone_identifiers_list($timezones[$_POST["timeZoneRegion"]]) as $tz){
        $tzs = explode("/", $tz, 2);
        echo  '<option value="' .$tzs[1]. '">' .$tzs[1]. '</option>' ;
   }       
}

?>
