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
require_once "database/invoice_table.php";
require_once "database/catering_item_table.php";
require_once "database/catering_order_table.php";
require_once "database/catering_recipe_table.php";
require_once "database/catering_recipe_item_table.php";
require_once "database/cash_closing_table.php";
require_once "database/cash_closing_data_table.php";
require_once "database/user_group_list_table.php";
require_once "database/item_required_days_table.php";

$readonly = $_SESSION["date"] <= date('Y-m-d', strtotime("-".$_SESSION["history_limit"])) ? "readonly" : "";

/*---------------manage_users.php-------------*/
if (isset($_POST["newRole"])) {
    UserRoleTable::update_user_role($_POST["roleUserName"], $_POST["newRole"]);
}

/*----------------------update_inventory.php-----------------*/
if (isset($_POST["itemQuantity"])) {
    echo InventoryTable::update_inventory($_POST["itemDate"], $_POST["itemId"], $_POST["itemQuantity"], $_POST["itemNote"]);
}
/*--------------edit_items.php------------*/
if (isset($_POST["updateItemQuantity"])) {
    BaseQuantityTable::update_base_quantity($_POST["itemId"], $_POST["quantity"]);
}

/*-----------------edit_categories.php-------------*/
if (isset($_POST["getCategorizedItems"])) {

    $result = ItemTable::get_categorized_items($_POST["getCategorizedItems"], $_POST["date"]);
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
    $order_number = 1;
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
        if(!ItemTable::add_new_item($_POST["itemName"], $_POST["itemUnit"], $_SESSION["date"], $_POST["itemPrice"],
                                    $_POST["itemDeviation"])) {
            echo 'item exists';
        } else {
            BaseQuantityTable::set_base_quantity($_POST["itemName"], $_POST["itemQuant"]);
            echo 'item added';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}

if(isset($_POST["getItems"])) {
    $result = ItemTable::get_items_categories($_SESSION["date"]);
    $current_category = 1;
    while($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="7" class="table_heading">'.$row["category_name"].'<span class="arrow_down float_right collapse_arrow"></span></td>
                </tr>';
        } else if ($row["category_name"] != $current_category AND $row["category_name"] == null) {
            $current_category = $row["category_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="7" class="table_heading">Uncategorized Items<span class="arrow_down float_right collapse_arrow"></span></td>
                </tr>';
        }
        echo '
            <tr>
                <td class="td_drawer">
                    <div class="div_tray">';
                        $day_ids = [];
                        $requiredItems = ItemRequiredDaysTable::get_item_days($row["id"]);
                        while ($item_row = $requiredItems->fetch_array()) {
                            $day_ids[]= $item_row[0];
                        }
                echo   '<span value="7" class="' .(in_array("7", $day_ids) ? "active" : ""). '">Sun</span>
                        <span value="1" class="' .(in_array("1", $day_ids) ? "active" : ""). '">Mon</span>
                        <span value="2" class="' .(in_array("2", $day_ids) ? "active" : ""). '">Tue</span>
                        <span value="3" class="' .(in_array("3", $day_ids) ? "active" : ""). '">Wed</span>
                        <span value="4" class="' .(in_array("4", $day_ids) ? "active" : ""). '">Thu</span>
                        <span value="5" class="' .(in_array("5", $day_ids) ? "active" : ""). '">Fri</span>
                        <span value="6" class="' .(in_array("6", $day_ids) ? "active" : ""). '">Sat</span>
                        <div class="close"></div>
                    </div>';
                    unset($day_ids);
        echo   '</td>
                <input type="hidden" class="item_id" name="item_id" value="'.$row["id"].'">
                <td class="td_checkbox">
                    <div class="checkbox">
                        <input type="checkbox" class="item_checkbox" name="checkbox[]" value="'.$row["id"].'" form="checkbox_form">
                        <span class="checkbox_style"></span>
                    </div>
                    <div class="calendar">
                        <span class="fa-calendar-plus-o"></span>
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
                    <td id="category" colspan="4" class="table_heading">'.$row["cat_name"].'<span class="arrow_down float_right collapse_arrow"></span></td>
                </tr>';
        } else if ($row["cat_name"] != $current_category AND $row["cat_name"] == null) {
            $current_category = $row["cat_name"];
            echo '
                <tr class="item_category_tr">
                    <td id="category" colspan="4" class="table_heading">Uncategorized Items<span class="arrow_down float_right collapse_arrow"></span></td>
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
    $item_ids = [];
    $required_day_id = date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"))), 'N');
    $required_item_ids = ItemRequiredDaysTable::get_by_day($required_day_id);
    while ($row = $required_item_ids->fetch_array()) {
        $item_ids[] = $row[0];
    }
    $result = CategoryTable::get_print_preview($_POST["date"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="7" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th></th>
                        <th class="item_heading">Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
        $quantity_required = $row["quantity_required"] == "" ? "-" : $row["quantity_required"];
        $cost_required = $row["cost_required"] == "" ? "-" : "$ ".$row["cost_required"];
        $required = in_array($row["item_id"], $item_ids) ? "true" : "false";

        echo '<tr id="column_data" class="row">
                <td class="row_icon">';
                if ($required == "true") {
                    echo '<span class="icon fa-star"></span>
                        <span class="text">required item</span>';
                }
          echo '</td>
                <td class="item_name ">'.$row["item_name"].'</td>
                <td>'.$row["unit"].'</td>
                <td>'.$row["quantity"].'</td>';
                if ($required == "true") {
                echo '<td class="quantity_required required" data-required ="'.$required.'" >
                        <div class="div_required">
                            <div class="div_tab">
                                <span class="tab fa-calculator selected" id="calculated"></span>
                                <span class="tab fa-pencil" id="custom"></span>
                            </div>
                            <div class="div_text">
                                <div class="heading">
                                    <span id="heading">calculated value</span>
                                </div>
                                <div class="div_value">
                                    <span class="span_qr">'.$quantity_required.'</span>
                                    <input type="number" class="span_qc" value="'.$row["quantity_custom"].'" onchange="updateQuantityCustom(this)"  placeholder="enter value" >
                                </div>
                            </div>
                        </div>
                     </td>';
                } else {
                 echo  '<td class="quantity_required" data-required ="'.$required.'" >'.$quantity_required.'</td>';

                }
        echo   '<td class="cost">'.$cost_required.'</td>
                <td id="td_notes">
                    <textarea name="" id="" rows="2" onchange="updateNotes(this); checkRequired();" value="'.$row["notes"].'" '.$readonly.' >'.$row["notes"].'</textarea>
                </td>
                <input id="hidden_id" type="hidden" value="'.$row["item_id"].'">
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
                    <tr id="category"><td colspan="5" class="table_heading">'.$row["category_name"].'</td></tr>
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
                    $quantity = (is_numeric($row["quantity"]) ? (BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_id"]) - $row["quantity"]): "-");
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

if (isset($_POST["getTrackedInvoice"])) {
    $result = CategoryTable::get_print_preview($_POST["date"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="6" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Required</th>
                        <th>Quantity Delivered</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                    $quantity_required = $row["quantity_custom"] == "" ? $row["quantity_required"] : $row["quantity_custom"];
                    $quantity_required = $quantity_required == "" ? "-" : $quantity_required;
                    $cost = is_numeric($row["cost_delivered"]) ? "$ ".$row["cost_delivered"] : "-";

        echo  '     <td>'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td class="quantity_required">'.$quantity_required.'</td>
                    <td><input  onchange="updateQuantity(this)" type="number" id="quantity_delivered" value="'.$row["quantity_delivered"].'" '.$readonly.' ></td>
                    <td class="cost">'.$cost.'</td>
                    <td id="td_notes">
                        <textarea name="" id="" rows="2" onchange="updateNotes(this)" value="'.$row["invoice_notes"].'" '.$readonly.' >'.$row["invoice_notes"].'</textarea>
                    </td>
                    <input type="hidden" value="'.$row["item_id"].'">
                </tr>';
    }
}

if (isset($_POST["getInventory"])) {
    $result = InventoryTable::get_inventory($_POST["categoryId"], $_POST["date"]);
    while ($row = $result -> fetch_assoc()) {
        $expected_quantity = $row["expected_quantity"] == "" ? "-" : $row["expected_quantity"];
        $warning = $row["has_deviation"] > 0 ? "warning_sign" : "";
        echo '<tr>
                <td class="item_name entypo-attention '.$warning.'">'.$row["name"].'</td>
                <td>'.$row["unit"].'</td>
                <td class="td_expected">'.$expected_quantity.'</td>
                <td class="td_quantity"><input class="quantity_input align_center" type="number" min="0" step="any" value="'.$row["quantity"].
                                        '" onchange="updateInventory(this); checkDeviation(this, true, true);" '.$readonly.' ></td>
                <td><input type="text" value="'.$row["notes"].'" onchange=updateInventory(this) '.$readonly.' ></td>
                <input type="hidden" value='.$row["id"].'>
                <input type="hidden" value='.$row["deviation"].'>
                <input type="hidden" id="cat_id" value='.$row["cat_id"].'>
            </tr>';
    }
}

if (isset($_POST["getCateringItems"])) {
    $result = CateringRecipeTable::get_recipes($_POST["orderId"]);
    if ($result->num_rows > 0) {
        echo '<tbody class="print_tbody" id="print_tbody">
                <tr id="category"><td colspan="4" class="table_heading">Recipes</td></tr>
                <tr id="category_columns">
                    <th>Recipe</th>
                    <th></th>
                    <th>Quantity</th>
                    <th>Notes</th>
                </tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr id="column_data" class="row">';
            echo  ' <td class="item_name recipe_item" >'.$row["name"].'</td><td></td>
                    <td><input  onchange="updateRecipeQuantity(this)" type="number" id="quantity_delivered" value="'.$row["quantity_required"].'"  '.$readonly.' ></td>
                    <td id="td_notes">
                        <textarea name="" id="" rows="2" onchange="updateRecipeNotes(this)" value="'.$row["notes"].'"  '.$readonly.' >'.$row["notes"].'</textarea>
                    </td>
                    <input type="hidden" value="'.$row["recipe_id"].'">
                </tr>';
        }
    }
    $result = CateringItemTable::get_items($_POST["orderId"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="4" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
        echo  '     <td class="item_name">'.$row["item_name"].'</td>
                    <td>'.$row["unit"].'</td>
                    <td><input  onchange="updateQuantity(this)" type="number" id="quantity_delivered" value="'.$row["quantity_required"].'"  '.$readonly.' ></td>
                    <td id="td_notes">
                        <textarea name="" id="" rows="2" onchange="updateNotes(this)" value="'.$row["notes"].'"  '.$readonly.' >'.$row["notes"].'</textarea>
                    </td>
                    <input type="hidden" value="'.$row["item_id"].'">
                </tr>';
    }
}

if (isset($_POST["getCateringItemTable"])) {
    $result = CateringItemTable::get_items_with_recipes($_POST["orderId"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="5" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Required</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">';
                $quantity = is_numeric($row["quantity_required"]) ? $row["quantity_required"] : "-";
                if (($quantity != "-" AND $quantity > -1) AND $row["price"] != "-") {
                    $cost = "$ ".round($quantity * $row["price"], 2);
                } else {
                    $cost = "-";
                }
        echo  ' <td>'.$row["item_name"].'</td>
                <td>'.$row["unit"].'</td>
                <td class="quantity_required">'.$quantity.'</td>
                <td class="cost">'.$cost.'</td>
                <td id="td_notes">
                    <textarea name="" id="" rows="2" onchange="updateCateringNotes(this); " value="'.$row["notes"].'"  '.$readonly.' >'.$row["notes"].'</textarea>
                    <input type="hidden" value="'.$row["item_id"].'">
                    <input type="hidden" value="'.$row["recipe_id"].'">
                </td>
            </tr>';
    }
}

if (isset($_POST["getCateringOrderInvoice"])) {
    $result = CateringItemTable::get_items_with_recipes($_POST["orderId"]);
    $current_category = null;
    while ($row = $result->fetch_assoc()) {
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="6" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Required</th>
                        <th>Quantity Delivered</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
                $quantity_required = is_numeric($row["quantity_required"]) ? $row["quantity_required"] : "-";
                $quantity = is_numeric($row["quantity_delivered"]) ? $row["quantity_delivered"] : "-";
                if (($quantity != "-" AND $quantity > -1) AND $row["price"] != "-") {
                    $cost = "$ ".round($quantity * $row["price"], 2);
                } else {
                    $cost = "-";
                }

        echo '<tr id="column_data" class="row">
                <td>'.$row["item_name"].'</td>
                <td>'.$row["unit"].'</td>
                <td class="quantity_required">'.$quantity_required.'</td>
                <td><input  onchange="updateCateringQuantity(this)" type="number" id="quantity_delivered" value="'.$row["quantity_delivered"].'" '.$readonly.' ></td>
                <td class="cost">'.$cost.'</td>
                <td id="td_notes">
                    <textarea name="" id="" rows="2" onchange="updateCateringNotes(this)" value="'.$row["invoice_notes"].'" '.$readonly.' >'.$row["invoice_notes"].'</textarea>
                </td>
                <input type="hidden" value="'.$row["item_id"].'">
                <input type="hidden" value="'.$row["recipe_id"].'">
            </tr>';
    }
}

if (isset($_POST["printAll"])) {
    $result = CategoryTable::get_print_preview($_POST["date"]);
    $current_category = null;
    $total_cost = "";
    echo '<table class="table_view"><tr class="row"><th class="table_title" colspan="6">Inventory</th></tr>
    <tr class="row"><th colspan="6" class="heading">Full Day</th></tr><tbody class="print_tbody" id="print_tbody">
            <tr id="print_date" class="row">
                <th colspan="6">
                    <span id="table_date_span">'.date_format((date_add(date_create($_POST["date"]), date_interval_create_from_date_string("1 day"))), 'D, jS M Y').'</span>
                    <div class="print_table_date">'."created on ".date('jS M Y', strtotime($_POST["date"])).'</div>
                </th>
            </tr>';
    echo $exp = $_POST["expectedSales"] != "" ? "<tr class='row'><th colspan='6' class='expected_heading'><span class='print_table_date'>Expected Sales</span>
                                            <span> $".$_POST["expectedSales"]."</span></th></tr>" : "";
    while ($row = $result->fetch_assoc()) {
        $quantity_custom = $row["quantity_custom"] == "" ? "-" : $row["quantity_custom"];
        $quantity_required = $row["quantity_required"] == "" ? "-" : $row["quantity_required"];
        $cost_required = $row["cost_required"] == "" ? "-" : "$ ".$row["cost_required"];

        if ($_POST["required"] == "true") {
            if ((($quantity_custom == "-") AND ($quantity_required <= 0 OR $quantity_required == "-")) AND $row["notes"] == "") {
              continue;
            }
        }
        if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
            $current_category = $row["category_name"];
            echo '
                    <tr id="category"><td colspan="6" class="table_heading">'.$row["category_name"].'</td></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Cost</th>
                        <th>Notes</th>
                    </tr>';
        }
        echo '<tr id="column_data" class="row">
                <td>'.$row["item_name"].'</td>
                <td>'.$row["unit"].'</td>
                <td>'.$row["quantity"].'</td>
                <td class="quantity_required">'.($quantity_custom != "-" ? $quantity_custom : $quantity_required).'</td>
                <td class="cost">'.$cost_required.'</td>
                <td id="td_notes">'.$row["notes"].'</td>
            </tr>';
    }
    $total_cost = $total_cost > 0 ? $total_cost : "-";
    echo '<tr><td class="table_heading" colspan="3"><h4>Total Cost</h4></td>
            <td class="table_heading" colspan="3"><h4>'.$total_cost.'</h4></td></tr></tbody></table>';
    $future_date = date_format((date_add(date_create($_POST["date"]), date_interval_create_from_date_string("2 day"))), 'Y-m-d');
    $orders = CateringOrderTable::get_orders_by_date($_POST["date"], $future_date);
    while ($row = $orders->fetch_assoc()) {
        $items = CateringItemTable::get_items_with_recipes($row["id"]);
        $current_category = null;
        $total_cost = "";
        $order_note = $row["notes"];
        echo '<pagebreak><table class="table_view"><tr class="row"><th class="table_title" colspan="6">Catering Order</th></tr>
        <tr class="row"><th colspan="6" class="heading">'.$row["name"].'</th></tr><tbody class="print_tbody" id="print_tbody">
        <tr id="print_date" class="row">
            <th colspan="6">
                <div id="table_date_heading">Delivery Date</div>
                <span id="table_date_span">'.date('D, jS M Y', strtotime($row["date_delivery"])).'</span>
                <div class="print_table_date">'."created on ".date('jS M Y', strtotime($row["date_created"])).'</div>
            </th>
        </tr>';
        while ($row = $items->fetch_assoc()) {
            $quantity = is_numeric($row["quantity_required"]) ? $row["quantity_required"] : "-";
            if (($quantity != "-" AND $quantity > -1) AND $row["price"] != "-") {
                $cost = "$ ".round($quantity * $row["price"], 2);
                $total_cost += round($quantity * $row["price"], 2);
            } else {
                $cost = "-";
            }
            if ($_POST["required"] == "true") {
                if (($quantity <= 0 OR $quantity == "-") AND $row["notes"] == "") {
                  continue;
                }
            }
            if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
                $current_category = $row["category_name"];
                echo '<tr id="category"><td colspan="5" class="table_heading">'.$row["category_name"].'</td></tr>
                        <tr id="category_columns">
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Quantity Required</th>
                            <th>Cost</th>
                            <th>Notes</th>
                        </tr>';
            }
            echo '<tr id="column_data" class="row">';
            echo  '     <td>'.$row["item_name"].'</td>
                        <td>'.$row["unit"].'</td>
                        <td class="quantity_required">'.$quantity.'</td>
                        <td class="cost">'.$cost.'</td>
                        <td id="td_notes">'.$row["notes"].'</td>
                    </tr>';
        }
        $total_cost = $total_cost > 0 ? $total_cost : "-";
        echo '<tr><td class="table_heading" colspan="3"><h4>Total Cost</h4></td>
            <td class="table_heading" colspan="3"><h4>'.$total_cost.'</h4></td></tr>';
        $order_note = $order_note == "" ? "No Special Instructions Added" : $order_note;
        echo '<tr id="category"><td colspan="6" class="table_title">Special Instructions</td></tr>
        <tr id="column_data" class="row" colspan="6"><td class="order_note" colspan="6">'.$order_note.'</td>
        </tbody></table>';
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

if (isset($_POST["addGroupUser"])) {
    echo UserGroupListTable::add_user($_POST["userId"], $_POST["groupId"]);
}

if (isset($_POST["deleteGroupUser"])) {
    echo UserGroupListTable::remove_user($_POST["userId"], $_POST["groupId"]);
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

if (isset($_POST["getGroupUsers"])) {
    $result = UserGroupListTable::get_users($_POST["groupId"]);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo '<li class="list_li grouped_item" user-id="'.$row["id"].'" group-id="'.$row["group_id"].'"
                    item-name="'.$row["username"].'">' .$row["username"];
        }
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

if (isset($_POST["trackInvoice"])) {
    echo InvoiceTable::track_invoice($_POST["date"]);
}

if (isset($_POST["updateQuantityDelivered"])) {
    echo InventoryTable::update_quantity_delivered($_POST["quantity"], $_POST["itemId"], $_POST["date"]);
}

if (isset($_POST["updateQuantityCustom"])) {
    echo InventoryTable::update_quantity_custom($_POST["quantity"], $_POST["itemId"], $_POST["itemDate"]);
}

if (isset($_POST["updateInvoiceNotes"])) {
    echo InventoryTable::update_invoice_note($_POST["note"], $_POST["itemId"], $_POST["date"]);
}

if (isset($_POST["getItemPrice"])) {
    echo ItemTable::get_item_price($_POST["itemId"]);
}

if (isset($_POST["removeInvoice"])) {
    echo InvoiceTable::remove_invoice($_POST["date"]);
}

if (isset($_POST["addCateringItem"])) {
    echo CateringItemTable::add_item($_POST["itemId"], $_POST["orderId"]);
}

if (isset($_POST["updateOrderRecipeItems"])) {
    echo CateringRecipeItemTable::add_recipe_items($_POST["recipeId"], $_POST["orderId"]);
}

if (isset($_POST["removeOrderRecipeItems"])) {
    echo CateringRecipeItemTable::remove_recipe_items($_POST["recipeId"], $_POST["orderId"]);
}

if (isset($_POST["updateOrderItemQuantity"])) {
    echo CateringRecipeItemTable::update_quantity_required($_POST["quantity"], $_POST["recipeId"], $_POST["orderId"]);
}

if (isset($_POST["removeCateringItem"])) {
    echo CateringItemTable::remove_item($_POST["itemId"], $_POST["orderId"]);
}

if (isset($_POST["addCateringRecipe"])) {
    echo CateringRecipeTable::add_recipe($_POST["itemId"], $_POST["orderId"]);
}

if (isset($_POST["removeCateringRecipe"])) {
    echo CateringRecipeTable::remove_recipe($_POST["itemId"], $_POST["orderId"]);
}

if (isset($_POST["updateCateringQuantity"])) {
    echo CateringItemTable::update_quantity($_POST["quantity"], $_POST["itemId"], $_POST["orderId"]);
}

if (isset($_POST["updateCateringNotes"])) {
    if (CateringItemTable::check_item($_POST["itemId"], $_POST["orderId"]) > 0) {
        echo CateringItemTable::update_notes($_POST["notes"], $_POST["itemId"], $_POST["orderId"]);
    } else {
        echo CateringRecipeItemTable::update_notes($_POST["notes"], $_POST["itemId"], $_POST["recipeId"], $_POST["orderId"]);
    }
}

if (isset($_POST["updateCateringRecipeQuantity"])) {
    echo CateringRecipeTable::update_quantity($_POST["quantity"], $_POST["recipeId"], $_POST["orderId"]);
}

if (isset($_POST["updateCateringRecipeNotes"])) {
    echo CateringRecipeTable::update_notes($_POST["notes"], $_POST["recipeId"], $_POST["orderId"]);
}

if (isset($_POST["updateCateringInvoiceNotes"])) {
    if (CateringItemTable::check_item($_POST["itemId"], $_POST["orderId"]) > 0) {
        echo CateringItemTable::update_invoice_notes($_POST["notes"], $_POST["itemId"], $_POST["orderId"]);
    } else {
       echo CateringRecipeItemTable::update_invoice_notes($_POST["notes"], $_POST["itemId"], $_POST["recipeId"], $_POST["orderId"]);
    }
}

if (isset($_POST["updateCateringInvoiceQuantity"])) {
    if (CateringItemTable::check_item($_POST["itemId"], $_POST["orderId"]) > 0) {
        echo CateringItemTable::update_quantity_delivered($_POST["quantity"], $_POST["itemId"], $_POST["orderId"]);
    } else {
       echo CateringRecipeItemTable::update_quantity_delivered($_POST["quantity"], $_POST["itemId"], $_POST["recipeId"], $_POST["orderId"]);
    }
}

if (isset($_POST["updateOrderInvoiceDate"])) {
    echo CateringOrderTable::update_order_invoice($_POST["orderId"], $_POST["date"]);
}

if (isset($_POST["updateOrderNote"])) {
    echo CateringOrderTable::update_order_note($_POST["note"], $_POST["orderId"]);
}

if (isset($_POST["updateCashRowName"])) {
    echo CashClosingTable::update_name($_POST["id"], $_POST["name"]);
}

if (isset($_POST["updateCashRowType"])) {
    echo CashClosingTable::update_type($_POST["id"], $_POST["type"]);
}

if (isset($_POST["updateCashClosingRow"])) {
    echo CashClosingDataTable::update_row($_POST["rowId"], $_POST["date"], $_POST["quantity"], $_POST["note"]);
}

if (isset($_POST["saveTodaysSales"])) {
    echo SalesTable::add_actual_sale($_POST["sales"], $_SESSION["date"]);
}

if (isset($_POST["addItemRequiredDay"])) {
    echo ItemRequiredDaysTable::add_item_day($_POST["dayId"], $_POST["itemId"]);
}

if (isset($_POST["removeItemRequiredDay"])) {
    echo ItemRequiredDaysTable::remove_item_day($_POST["dayId"], $_POST["itemId"]);
}

if (isset($_POST["UpdateCashClosingOrder"])) {
    $order_number = 0;
    foreach ($_POST["rowIds"] as $value) {
        CashClosingTable::update_row_order($value, $order_number);
        $order_number++;
    }
}

if (isset($_POST["updateDeviation"])) {
    echo InventoryTable::update_item_deviation($_POST["deviation"], $_POST["itemId"], $_POST["date"]);
}

if (isset($_POST["updateCostDelivered"])) {
    echo InventoryTable::update_cost_delivered($_POST["cost"], $_POST["itemId"], $_POST["date"]);
}

if (isset($_POST["calcExpected"])) {
    $date = date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("-1 day"))), 'Y-m-d');
    $estimated_sales = SalesTable::get_expected_sale($date);
    $todays_sales = $_POST["todaysSale"];
    $base_sale = VariablesTable::get_base_sales();
    $result = ItemTable::get_items();
    while ($row = $result -> fetch_assoc()) {
        if ($todays_sales == "NULL" OR is_null($estimated_sales)) {
            $expected_quantity = 'NULL';
        } else {
            $quantity_factor = $row["quantity"] / $base_sale;
            $estimated_quantity = $estimated_sales * $quantity_factor;
            $todays_quantity = $todays_sales * $quantity_factor;
            $expected_quantity = $estimated_quantity - $todays_quantity;
            $expected_quantity = $expected_quantity < 0 ? 0 : $expected_quantity;
            if ($row["rounding_option"] == "up") {
                $expected_quantity = ceil($expected_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else if ($row["rounding_option"] == "down") {
                $expected_quantity = floor($expected_quantity / $row["rounding_factor"]) * $row["rounding_factor"];
            } else {
                $expected_quantity = round($expected_quantity, 2);
            }
        }
        echo InventoryTable::update_expected_quantity($expected_quantity, $row["id"], $_SESSION["date"]);
    }
}

if (isset($_POST["calcQuantityRequired"])) {
    $expected_sales = $_POST["expectedSales"];
    $result = CategoryTable::get_print_preview($_SESSION["date"]);
    while ($row = $result -> fetch_assoc()) {
        if (is_numeric($expected_sales)) {
            $sales_factor = $expected_sales / VariablesTable::get_base_sales();
            if (is_numeric($row["quantity"])) {
                $quantity = BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_id"]) - $row["quantity"];
                if ($row["rounding_option"] == "up") {
                    $quantity = ceil($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                } else if ($row["rounding_option"] == "down") {
                    $quantity = floor($quantity / $row["rounding_factor"]) * $row["rounding_factor"];
                }
            } else {
                $quantity = 'NULL';
            }
        } else {
            $quantity = 'NULL';
        }
        if (($quantity != "NULL" AND $quantity > 0) AND $row["price"] != "-") {
            $cost = round($quantity * $row["price"], 2);
        } else {
            $cost = "NULL";
        }
        echo InventoryTable::update_quantity_required($quantity, $row["item_id"], $_SESSION["date"]);
        echo InventoryTable::update_cost_required($cost, $row["item_id"], $_SESSION["date"]);
    }
}

?>
