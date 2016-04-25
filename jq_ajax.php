<?php
session_start();
require_once "database/user_table.php";
require_once "database/user_role_table.php";
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/inventory_table.php";
require_once "database/base_quantity_table.php";
require_once "database/variables_table.php";
require_once "database/conversation_table.php";
require_once "database/timeslot_table.php";
require_once "database/timeslot_item_table.php";

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
    UserRoleTable::update_user_role($_POST["roleUserName"], $_POST["newRole"]);
}

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    InventoryTable::update_inventory($_POST["itemDate"], $_POST["itemId"], $_POST["itemQuantity"], $_POST["itemNote"]);
}

/*-----------------------print_preview.php---------------*/
if (isset($_POST["sales"])) {
    VariablesTable::update_expected_sales($_POST["sales"]);
}

/*--------------edit_items.php------------*/
if (isset($_POST["quantity"])) {
    BaseQuantityTable::update_base_quantity($_POST["itemId"], $_POST["quantity"]);
}

/*-----------------edit_categories.php-------------*/
if (isset($_POST["getCategorizedItems"])) {

    $result = ItemTable::get_categorized_items($_POST["getCategorizedItems"]);
    if ($result) {
        echo '<ul class="category_list" id="categorized_list" >';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list_li" id="'.$row["id"].'" item-name="'.$row["name"].'">' .$row["name"]. ' </li>';
        }
         echo '</ul>';
    }
}

if (isset($_POST["UpdateItemOrder"])) {
    $order_number = 0;
    foreach ($_POST["itemIds"] as $value) {
        ItemTable::update_item_order($value, $order_number);
        $order_number++;
    }
}

if (isset($_POST["UpdateCategoryOrder"])) {
    $order_number = 0;
    foreach ($_POST["categoryIds"] as $value) {
        CategoryTable::update_category_order($value, $order_number);
        $order_number++;
    }
}

if (isset($_POST["UpdateTimeslotOrder"])) {
    $order_number = 0;
    foreach ($_POST["timeslotNames"] as $value) {
        TimeslotTable::update_timeslot_order($value, $order_number);
        $order_number++;
    }
}

/*----------------edit_categories.php----------------*/
if (isset($_POST["UpdateItemsCategory"])) {
    ItemTable::update_items_category($_POST["categoryName"], $_POST["itemName"]);
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

/*--------------------------messages.php----------------------------*/
if (isset($_POST["sessionName"])) {
    echo ConversationTable::count_unread_conversations($_POST["sessionName"]);
}

/*--------------------------------received_messages.php----------------*/
if (isset($_POST["checkedId"])) {
    echo ConversationTable::update_multiple_conversation_status($_SESSION["username"], $_POST["checkedId"], $_POST["newStatus"]);
}

if(isset($_POST["getItemCount"])) {
    echo ItemTable::get_items_count();
}

if(isset($_POST["addItem"])) {
     try {
        if(!ItemTable::add_new_item($_POST["itemName"], $_POST["itemUnit"])) {
            echo '<div class="error">Item already exists</div>';
        } else {
            if (!empty($_POST["itemQuant"])) {
                BaseQuantityTable::set_base_quantity($_POST["itemName"], $_POST["itemQuant"]);
            }
            echo '<div class="error">Item added successfully</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}

if(isset($_POST["getItems"])) {
    $result = ItemTable::get_items();
    while($row = $result->fetch_assoc()) {
      echo ' <tr>
            <td class="td_checkbox">
                <input type="checkbox" class="item_checkbox" name="checkbox[]" value="'.$row["id"].'" form="checkbox_form">
            </td>
            <td><input type="text" name="item_name" value="'.$row["name"].'" onchange=updateItem(this) class="align_center"></td>
            <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange=updateItem(this) class="align_center"></td>
            <td><input type="number" name="item_quantity" step="any" min="0" value="'.$row["quantity"].'" onchange=quantityChange(this) class="align_center"></td>
            <input type="hidden" name="item_id" value="'.$row["id"].'">
        </tr>';
    }
}

if(isset($_POST["getItemsInRange"])) {
    $result = ItemTable::get_items_in_range($_POST["offset"], $_POST["limit"]);
    while ($row = $result->fetch_assoc()) {
    echo ' <tr>
            <form action="edit_items.php" method="post">
            <td><input type="text" name="item_name" value="'.$row["name"].'" onchange="this.form.submit()" class="align_center"></td>
            <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange="this.form.submit()" class="align_center"></td>
            <td><input type="number" name="item_quantity" step="any" min="0" value="'.$row["quantity"].'" onchange=quantityChange(this) class="align_center"></td>
            <input type="hidden" name="item_id" value="'.$row["id"].'">
            </form>
            <td>
                <form action="edit_items.php" method="post" onsubmit="return confirm(\'delete this item?\');">
                    <input type="hidden" name="delete_item" value="'.$row["name"].'">
                    <input type="submit" value="delete" class="button" >
                </form>
            </td>
        </tr>';
    }
}

if(isset($_POST["getItemsTimeSlot"])) {
    $result = ItemTable::get_items_by_timeslot($_POST["timeSlotName"]);
    while ($row = $result->fetch_assoc()) {
    echo ' <tr>
            <form action="edit_items.php" method="post">
            <td class="td_checkbox"></td>
            <td><input type="text" class="align_center item_name" name="item_name" value="'.$row["name"].'" onchange="this.form.submit()"></td>
            <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange="this.form.submit()" class="align_center"></td>
            <td><input type="number" name="item_quantity" step="any" min="0" value="'.$row["factor"].'" onchange=factorChange(this) class="align_center"></td>
            <input type="hidden" name="tsi_id" value="'.$row["tsi_id"].'">
            </form>
            <td>
                <form action="edit_items.php" method="post" onsubmit="return confirm(\'delete this item?\');">
                    <input type="hidden" name="delete_item" value="'.$row["name"].'">
                    <input type="submit" value="delete" class="button" >
                </form>
            </td>
        </tr>';
    }
}

if(isset($_POST["getCategoryItemsTimeSlot"])) {
    $result = ItemTable::get_category_items_by_timeslot($_POST["timeSlotName"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["cat_name"] != $current_category AND $row["cat_name"] != null) {
            $current_category = $row["cat_name"];
            echo '
                <tr>
                    <td colspan="4"><h4>'.$row["cat_name"].'</h4></td>
                </tr>';
        }
    echo '
        <tr>
            <td class="td_checkbox"></td>
            <td><input type="text" class="align_center item_name" name="item_name" value="'.$row["name"].'" onchange="this.form.submit()" readonly></td>
            <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange="this.form.submit()" class="align_center" readonly></td>
            <td><input type="number" name="item_quantity" step="0.01" min="0.00" max="1.00" value="'.$row["factor"].'" onchange=factorChange(this) class="align_center"></td>
            <input type="hidden" name="tsi_id" value="'.$row["tsi_id"].'">
        </tr>';
    }
}

if (isset($_POST["getPrintPreview"])) {
    $result = CategoryTable::get_print_preview($_POST["date"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="5" class="none"><h4>'.$row["category_name"].'</h4></td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                    $sales_factor = VariablesTable::get_expected_sales() / VariablesTable::get_base_sales();
        echo  '     <td>'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td>'.$row["quantity"].'</td>
                    <td class="quantity_required">'.(is_numeric($row["quantity"]) ? BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - $row["quantity"] : "-").'</td>
                    <td class="align_left">'.$row["notes"].'</td>
                </tr>';
    }
}

if(isset($_POST["getPrintPreviewTimeslots"])) {
    $result = CategoryTable::get_print_preview_timeslots($_POST["date"], $_POST["timeSlotName"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="5" class="none"><h4>'.$row["category_name"].'</h4></td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                    $sales_factor = VariablesTable::get_expected_sales() / VariablesTable::get_base_sales();
                    $quantity = (is_numeric($row["quantity"]) ? (BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - $row["quantity"]) * $row["factor"] : "-");
        echo  '     <td>'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td>'.$row["quantity"].'</td>
                    <td class="quantity_required">'.($quantity == '-0' ? abs($quantity) : $quantity).'</td>
                    <td class="align_left">'.$row["notes"].'</td>
                </tr>';
    }
}


if(isset($_POST["AddTimeslotItem"])) {
   echo TimeslotItemTable::add_timeslot_item($_POST["itemName"], $_POST["timeslotName"]);
}

if(isset($_POST["RemoveTimeslotItem"])) {
    echo TimeslotItemTable::remove_timeslot_item($_POST["itemName"], $_POST["timeslotName"]);
}

if (isset($_POST["UpdateTimeslotFactor"])) {
    echo TimeslotItemTable::update_timeslot_factor($_POST["tsiId"], $_POST["factor"]);
}
if (isset($_POST["updateItems"])) {
    echo ItemTable::update_item_details($_POST["itemId"], $_POST["itemName"], $_POST["itemUnit"]);
}

?>
