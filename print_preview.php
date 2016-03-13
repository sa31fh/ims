<?php
session_start();
require_once "database/category_table.php";
require_once "database/variables_table.php";
require_once "database/base_quantity_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["expected_sales"])) {
    VariablesTable::update_expected_sales($_POST["expected_sales"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Preview</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="toolbar_print">
        <input class="option" type="button" onClick=goBack() value="Back">
        <div class="divider"></div>
        <div class="toolbar_div">
            <input class="toolbar_checkbox" type="checkbox" id="hide_checkbox"> <span id="hide_label">All</span>
        </div> <div class="divider"></div>
        <?php if ($_SESSION["userrole"] == "admin"): ?>
        <div class="toolbar_div">
            <form action="print_preview.php" method="post">
            <span >Expected Sales ($):</span>
            <input class="print_expected" type="number" name="expected_sales" value="<?php echo VariablesTable::get_expected_sales() ?>" onchange="this.form.submit()">
            </form>
        </div>
        <div class="divider"></div>
        <?php endif ?>
        <div class="toolbar_div">
            <a  id="print_share" class="option" onclick=sendPrint()>Share</a>
        </div>
    </div>
    <div id="table">
        <table class="user_table" id="print">
            <tr id="print_date" class="row">
                <th colspan="5"><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></th>
            </tr>
            <?php $current_category = null;
                  $result = CategoryTable::get_print_preview($_SESSION["date"]); ?>
            <?php while ($row =$result->fetch_assoc()): ?>
                <?php if ($row["category_name"] != $current_category): ?>
                    <?php $current_category = $row["category_name"] ?>
                <tbody class="print_tbody">
                    <tr id="category"><th colspan="5"><?php echo $current_category ?></th></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Notes</th>
                    </tr>
                <?php endif ?>
                <tr id="column_data" class="row">
                    <?php $sales_factor = VariablesTable::get_expected_sales() / VariablesTable::get_base_sales(); ?>
                    <td><?php echo $row["item_name"] ?></td>
                    <td><?php echo $row["unit"] ?></td>
                    <td><?php echo $row["quantity"] ?></td>
                    <td class="quantity_required"><?php echo (is_numeric($row["quantity"]) ? BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - $row["quantity"] : "-") ?></td>
                    <td><?php echo $row["notes"] ?></td>
                </tr>
            <?php endwhile ?>
            </tbody>
        </table>
    </div>
    <form action="messages.php" id="print_form" method="post">
        <input type="hidden" id="print_data" name="print_data">
    </form>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function goBack() {
        location.assign("category_status.php");
    }
    function sendPrint(){
        var dat = document.getElementById("table").innerHTML;
        document.getElementById("print_data").value = dat;
        document.getElementById("print_form").submit();
    }

    $(document).ready(function(){
        $("#hide_checkbox").change(function(){
            if ($(this).prop("checked")) {
                $(".print_tbody").each(function(){
                    var total = $(this).find(".quantity_required").length;
                    var remove = 0;
                    $(this).find(".quantity_required").each(function(){
                      if (this.innerHTML < 0 || this.innerHTML == "-") {
                        $(this).parent().hide();
                        remove++;
                      }
                    });
                    if (total - remove == 0) {
                        $(this).hide();
                    }
                });
                $("#hide_label").text("Required");
            } else {
                $(".print_tbody").each(function(){
                    $(this).show();
                    $(this).find("tr").show();
                    $("#hide_label").text("All");
                });
            }
        });
    });
</script>