<?php
    require_once "database/conversation_table.php";
    require_once "database/inventory_table.php";
    require_once "database/user_table.php";
    require_once "database/variables_table.php";
    require_once "database/sales_table.php";
    require_once "database/base_quantity_table.php";

    $inventory_data = InventoryTable::get_inventory_with_deviation(date("Y-m-d", strtotime("yesterday")));

    if ($inventory_data->num_rows > 0) {

        $admin_users = UserTable::get_admin_users();
        $date = date("Y-m-d", strtotime("-2 days"));
        $expected_sales = SalesTable::get_expected_sale($date);
        $actual_sales = SalesTable::get_actual_sale(date("Y-m-d", strtotime("yesterday")));
        $base_sale = VariablesTable::get_base_sales();
        $current_category = null;
        $message = "Item Deviation Report for ".date('d F Y', strtotime("yesterday"));
        $attachment =
            '<table class="table_view" id="print">
                <tr class="row">
                    <th colspan="6" class="heading">Item Deviation Report</th>
                </tr>
                <tr id="print_date" class="row">
                    <th colspan="6">
                        <div class="print_table_date">'."created on ".date('D, M d Y', strtotime("yesterday")).'</div>
                    </th>
                </tr>';

        while ($row = $inventory_data->fetch_assoc()) {
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
            if ($row["quantity"] > 0) {
                $current_deviation = (abs($row["quantity"] - $estimated_quantity) * 100) / $row["quantity"];
            } else {
                $current_deviation = (abs($row["quantity"] - $estimated_quantity) * 100) / 1;
            }
            $current_deviation = round($current_deviation, 2);
            if ($row["deviation"] < $current_deviation AND $row["quantity"] != "") {
                if ($row["category_name"] != $current_category AND $row["category_name"] != null) {
                    $current_category = $row["category_name"];
                $attachment .=
                '<tbody class="print_tbody" id="print_tbody">
                    <tr id="category"><td colspan="6" class="table_heading"><h4 class="none" >'.$row["category_name"].'</h4></td></tr>
                    <tr id="category_columns">
                        <th>Item</th><th>Unit</th><th>Expected Quantity</th><th>Quantity Present</th><th>Accepted Deviation</th><th>Current Deviation</th>
                    </tr>';
                }
                $attachment .=
                '<tr id="column_data" class="row">
                <td>'.$row["name"].'</td>
                <td>'.$row["unit"].'</td>
                <td>'.$estimated_quantity.'</td>
                <td>'.$row["quantity"].'</td>
                <td>'.$row["deviation"].' %'.'</td>
                <td>'.$current_deviation.' %'.'</td>
                </tr>';
            }
        }

        while ($user = $admin_users->fetch_assoc()) {
            ConversationTable::create_conversation("System", $user["username"], "Daily Item Deviation Report",
                    $message, gmdate("Y-m-d H:i:s"), $attachment, "Item Deviation Report", "read", "unread");
        }

    }
?>