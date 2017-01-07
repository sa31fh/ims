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
require_once "database/recipe_item_table.php";
require_once "database/recipe_table.php";
require_once "database/sales_table.php";
require_once "database/notification_status_table.php";
require_once "database/sub_notification_status_table.php";

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
    UserRoleTable::update_user_role($_POST["roleUserName"], $_POST["newRole"]);
}

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    echo InventoryTable::update_inventory($_POST["itemDate"], $_POST["itemId"], $_POST["itemQuantity"], $_POST["itemNote"]);
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
    $result = ItemTable::get_items_categories();
    $current_category = 1;
    while($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="7" class="table_heading"><h4 class="none">'.$row["category_name"].'<span class="arrow_down float_right collapse_arrow"></span></h4></td>
                </tr>';
        } else if ($row["category_name"] != $current_category AND $row["category_name"] == null) {
            $current_category = $row["category_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="7" class="table_heading"><h4 class="none">'."Uncategorized Items".'<span class="arrow_down float_right collapse_arrow"></span></h4></td>
                </tr>';
        }
        echo '
            <tr>
                <input type="hidden" name="item_id" value="'.$row["id"].'">
                <td class="td_checkbox">
                    <div class="checkbox">
                        <input type="checkbox" class="item_checkbox" name="checkbox[]" value="'.$row["id"].'" form="checkbox_form">
                        <span class="checkbox_style"></span>
                    </div>
                </td>
                <td><input type="text" name="item_name" value="'.$row["name"].'" onchange=updateItem(this) class="align_center item_name"></td>
                <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange=updateItem(this) class="align_center"></td>
                <td><input type="number" name="item_quantity" step="any" min="0" value="'.$row["quantity"].'" onchange=quantityChange(this) class="align_center number_view"></td>
                <td>$<input type="number" name="item_price" step="any" min="0" value="'.$row["price"].'" onchange=updateItem(this) class="align_center number_view"></td>
                <td><input type="number" name="item_deviation step="1" min="0" value="'.$row["deviation"].'" onchange=updateItemDeviation(this) class="align_center number_view">%</td>
                <td id="round_tr">
                    <select name="" id="" onchange=updateRoundingOption(this)>
                        <option value="none" '; if ($row["rounding_option"] == "none") {echo "selected";} echo'>none</option>
                        <option value="up" '; if ($row["rounding_option"] == "up") {echo "selected";} echo'>up</option>
                        <option value="down" '; if ($row["rounding_option"] == "down") {echo "selected";} echo'>down</option>
                    </select>
                    <input id="round_input" type="number" step="any" value="'.$row["rounding_factor"].'" onchange=updateRoundingFactor(this)
                            class="align_center" '; if ($row["rounding_option"] == "none") {echo "disabled";} echo'>
                </td>
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
    $current_category = 1;
    while ($row = $result->fetch_assoc()) {
        if ($row["cat_name"] != $current_category AND $row["cat_name"] != null) {
            $current_category = $row["cat_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="4" class="table_heading"><h4 class="none">'.$row["cat_name"].'<span class="arrow_down float_right collapse_arrow"></span></h4></td>
                </tr>';
        } else if ($row["cat_name"] != $current_category AND $row["cat_name"] == null) {
            $current_category = $row["cat_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="4" class="table_heading"><h4 class="none">'."Uncategorized Items".'<span class="arrow_down float_right collapse_arrow"></span></h4></td>
                </tr>';
        }
    echo '
        <tr>
            <td class="td_checkbox"></td>
            <td><input type="text" class="align_center item_name" name="item_name" value="'.$row["name"].'" onchange="this.form.submit()" readonly></td>
            <td><input type="text" name="item_unit" value="'.$row["unit"].'" onchange="this.form.submit()" class="align_center" readonly></td>
            <td><input type="text" name="item_quantity" value="'.$row["factor"].'" onchange=factorChange(this) class="align_center"></td>
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
                    <tr id="category"><td colspan="6" class="table_heading"><h4 class="none" >'.$row["category_name"].'</h4></td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                    $sales_factor = SalesTable::get_expected_sale($_SESSION["date"]) / VariablesTable::get_base_sales();
                    $quantity = (is_numeric($row["quantity"]) ? BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - $row["quantity"] : "-");
                    if ($row["rounding_option"] == "up") {
                        $quantity = ceil($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                    } else if ($row["rounding_option"] == "down") {
                        $quantity = floor($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                    }
                    if (($quantity != "-" AND $quantity > -1) AND $row["price"] != "-") {
                        $cost = "$ ".round($quantity * $row["price"], 2);
                    } else {
                        $cost = "-";
                    }
        echo  '     <td>'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td>'.$row["quantity"].'</td>
                    <td class="quantity_required">'.$quantity.'</td>
                    <td>'.$cost.'</td>
                    <td id="td_notes">
                        <textarea name="" id="" rows="2" onchange="updateNotes(this); checkRequired();" value="'.$row["notes"].'">'.$row["notes"].'</textarea>
                        <input type="hidden" value="'.$row["item_id"].'">
                    </td>
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
                    <tr id="category"><td colspan="5" class="table_heading"><h4 class="none">'.$row["category_name"].'</h4></td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                    $sales_factor = SalesTable::get_expected_sale($_SESSION["date"]) / VariablesTable::get_base_sales();
                    $quantity = (is_numeric($row["quantity"]) ? (BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - $row["quantity"]): "-");
                    if ($quantity != "-") {
                        $quantity = eval("return ".str_replace('x', $quantity, $row["factor"]).";");
                        if ($row["rounding_option"] == "up") {
                            $quantity = ceil($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                        } else if ($row["rounding_option"] == "down") {
                            $quantity = floor($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                        }
                    }
        echo  '     <td>'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td>'.$row["quantity"].'</td>
                    <td class="quantity_required">'.($quantity == '-0' ? abs($quantity) : $quantity).'</td>
                    <td class="align_left">'.$row["notes"].'</td>
                </tr>';
    }
}

if (isset($_POST["getInventory"])) {
    $result = InventoryTable::get_inventory($_POST["categoryId"], $_POST["date"]);
    $date = date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("-1 day"))), 'Y-m-d');
    $expected_sales = SalesTable::get_expected_sale($date);
    $actual_sales = SalesTable::get_actual_sale($_SESSION["date"]);
    $base_sale = VariablesTable::get_base_sales();
    while ($row = $result -> fetch_assoc()) {
        if (is_null($actual_sales) OR is_null($expected_sales)) {
            $estimated_quantity = "-";
        } else {
            $quantity_factor = BaseQuantityTable::get_base_quantity($row["name"]) / $base_sale;
            $expected_quantity = $expected_sales * $quantity_factor;
            $actual_quantity = $actual_sales * $quantity_factor;
            $estimated_quantity = $expected_quantity - $actual_quantity;
            $estimated_quantity = $estimated_quantity < 0 ? 0 : $estimated_quantity;
            if ($row["rounding_option"] == "up") {
                $estimated_quantity = ceil($estimated_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else if ($row["rounding_option"] == "down") {
                $estimated_quantity = floor($estimated_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else {
                $estimated_quantity = round($estimated_quantity, 2);
            }
        }
        echo '<tr>
                <td class="item_name entypo-attention">'.$row["name"].'</td>
                <td>'.$row["unit"].'</td>
                <td class="td_expected">'.$estimated_quantity.'</td>
                <td class="td_quantity"><input class="quantity_input align_center" type="number" min="0" step="any" value="'.$row["quantity"].
                                        '" onchange="updateInventory(this); checkDeviation(this, true, true);" ></td>
                <td><input type="text" value="'.$row["notes"].'" onchange=updateInventory(this)></td>
                <input type="hidden" value='.$row["id"].'>
                <input type="hidden" value='.$row["deviation"].'>
            </tr>';
    }
}

if (isset($_POST["getSearchInventory"])) {
    $result = InventoryTable::get_search_inventory($_POST["date"]);
    $date = date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("-1 day"))), 'Y-m-d');
    $expected_sales = SalesTable::get_expected_sale($date);
    $actual_sales = SalesTable::get_actual_sale($_SESSION["date"]);
    $base_sale = VariablesTable::get_base_sales();
    while ($row = $result -> fetch_assoc()) {
        if (is_null($actual_sales) OR is_null($expected_sales)) {
            $estimated_quantity = "-";
        } else {
            $quantity_factor = BaseQuantityTable::get_base_quantity($row["name"]) / $base_sale;
            $expected_quantity = $expected_sales * $quantity_factor;
            $actual_quantity = $actual_sales * $quantity_factor;
            $estimated_quantity = $expected_quantity - $actual_quantity;
            $estimated_quantity = $estimated_quantity < 0 ? 0 : $estimated_quantity;
            if ($row["rounding_option"] == "up") {
                $estimated_quantity = ceil($estimated_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else if ($row["rounding_option"] == "down") {
                $estimated_quantity = floor($estimated_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else {
                $estimated_quantity = round($estimated_quantity, 2);
            }
        }
        echo '<tr>
                <td class="item_name entypo-attention">'.$row["name"].'</td>
                <td>'.$row["unit"].'</td>
                <td class="td_expected">'.$estimated_quantity.'</td>
                <td class="td_quantity"><input class="quantity_input align_center" type="number" min="0" step="any" value="'.$row["quantity"].
                                        '" onchange="updateInventory(this); checkDeviation(this, true, true);" ></td>
                <td><input type="text" value="'.$row["notes"].'" onchange=updateInventory(this)></td>
                <input type="hidden" value='.$row["id"].'>
                <input type="hidden" value='.$row["deviation"].'>
            </tr>';
    }
}

if (isset($_POST["AddTimeslotItem"])) {
   echo TimeslotItemTable::add_timeslot_item($_POST["itemName"], $_POST["timeslotName"]);
}

if (isset($_POST["RemoveTimeslotItem"])) {
    echo TimeslotItemTable::remove_timeslot_item($_POST["itemName"], $_POST["timeslotName"]);
}

if (isset($_POST["UpdateTimeslotFactor"])) {
    echo TimeslotItemTable::update_timeslot_factor($_POST["tsiId"], $_POST["factor"]);
}

if (isset($_POST["updateItems"])) {
    echo ItemTable::update_item_details($_POST["itemId"], $_POST["itemName"], $_POST["itemUnit"], $_POST["itemPrice"]);
}

if (isset($_POST["updateItemDeviation"])) {
    echo ItemTable::update_deviation($_POST["deviation"], $_POST["itemId"]);
}

if (isset($_POST["updateRoundingOption"])) {
    echo ItemTable::update_rounding_option($_POST["roundingOption"], $_POST["itemId"]);
}

if (isset($_POST["updateRoundingFactor"])) {
    echo ItemTable::update_rounding_factor($_POST["roundingFactor"], $_POST["itemId"]);
}

if (isset($_POST["addRecipeItem"])) {
    echo RecipeItemTable::add_recipe_item($_POST["itemId"], $_POST["recipeId"]);
}

if (isset($_POST["deleteRecipeItem"])) {
    echo RecipeItemTable::delete_recipe_item($_POST["itemId"], $_POST["recipeId"]);
}

if (isset($_POST["getRecipeItems"])) {
    $result = RecipeItemTable::get_recipe_items($_POST["recipeId"]);
    if ($result) {
        echo '<ul class="category_list" id="categorized_list" >';
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list_li recipe_item" recipe-item-id="'.$row["id"].'" item-name="'.$row["name"].'">' .$row["name"]. '
                  <input type="number" value="'.$row["quantity"].'" onchange=updateQuantity(this)></li>';
        }
         echo '</ul>';
    }
}

if (isset($_POST["updateRecipeInventoryQuantity"])) {
    echo RecipeItemTable::update_recipe_inventory_quantity($_POST["recipeItemId"], $_POST["quantity"]);
}

if (isset($_POST["setNotiStatus"])) {
    echo NotificationStatusTable::set_notification_status($_POST["user_name"], $_POST["notification_id"], $_POST["status"]);
}

if (isset($_POST["setSubNotiStatus"])) {
    echo SubNotificationStatusTable::set_notification_status($_POST["user_name"], $_POST["notification_id"], $_POST["status"], $_POST["parent_noti_id"]);
}

if (isset($_POST["setUserEmail"])) {
    echo UserTable::update_user_email($_POST["userName"], $_POST["email"]);
}

?>
