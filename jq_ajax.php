<?php 
require_once "database/user_table.php";
require_once "database/user_role_table.php";
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/inventory_table.php";
require_once "database/base_quantity_table.php";
require_once "database/variables_table.php";
require_once "database/conversation_table.php";

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
    UserRoleTable::update_user_role($_POST["roleUserName"], $_POST["newRole"]);
}

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    InventoryTable::update_inventory($_POST["itemDate"], $_POST["itemId"], $_POST["itemQuantity"], $_POST["itemNote"]);
}

/*-----------------------category_status.php---------------*/
if (isset($_POST["sales"])) {
    VariablesTable::update_expected_sales($_POST["sales"]);
}

/*--------------edit_items.php------------*/
if (isset($_POST["quantity"])) {
    BaseQuantityTable::update_base_quantity($_POST["itemId"], $_POST["quantity"]);
}

/*-----------------edit_categories.php-------------*/
if (isset($_POST["showCategorizedItems"])) {
    
    $result = ItemTable::get_categorized_items($_POST["showCategorizedItems"]);
    if ($result) {
        echo '<select class="category_select" id="categorized_list" size=8>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' .$row["name"]. '">' .$row["name"]. ' </option>';
        }
         echo '</select>';
    }
}

/*----------------edit_categories.php----------------*/
if (isset($_POST["items"])) {
    ItemTable::update_items_category($_POST["categoryName"], $_POST["items"]);
}

/*---------user_account.php--------------*/
if (isset($_POST["userName"])) {
    
    if (UserTable::verify_credentials($_POST["userName"], $_POST['password'])) {
        echo "true";
    }  else {
        echo "false";
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

if (isset($_POST["sessionName"])) {
    echo ConversationTable::count_unread_conversations($_POST["sessionName"], $_POST["status"]);
}

?>
