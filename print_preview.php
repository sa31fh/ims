<?php
require_once "database/category_table.php";
require_once "database/variables_table.php";
require_once "database/base_quantity_table.php";
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
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
    <div>
        <a href="category_status.php" class="buttonBack">Back</a>
        <input type="button" onclick=sendPrint() value="Send Table" class="button">
    </div>

    <div>
        <table class="user_table" id="print">
            <tr id="print_date">
                <th colspan="5"><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></th>
            </tr>
            <?php $current_category = null;
                  $result = CategoryTable::get_print_preview($_SESSION["date"]); ?>
            <?php while ($row =$result->fetch_assoc()): ?>
                <?php if ($row["category_name"] != $current_category): ?>
                    <?php $current_category = $row["category_name"] ?>
                    <tr id="category"><th colspan="5"><?php echo $current_category ?></th></tr>
                    <tr id="category_columns">
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Quantity Required</th>
                        <th>Notes</th>
                    </tr>
                <?php endif ?>
                <tr>
                    <?php $sales_factor = VariablesTable::get_expected_sales() / VariablesTable::get_base_sales(); ?>
                    <td><?php echo $row["item_name"] ?></td>
                    <td><?php echo $row["unit"] ?></td>
                    <td><?php echo $row["quantity"] ?></td>
                    <td><?php echo (is_numeric($row["quantity"]) ? BaseQuantityTable::get_estimated_quantity($sales_factor, $row["item_name"]) - (int)$row["quantity"] : "-") ?></td>
                    <td><?php echo $row["notes"] ?></td>
                </tr>
            <?php endwhile ?>
        </table>
    </div>
    <form action="messages.php" id="print_form" method="post">
        <input type="hidden" id="print_data" name="print_data">
    </form>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function sendPrint(){
        var dat = document.getElementById("table").innerHTML;
        document.getElementById("print_data").value = dat;
        document.getElementById("print_form").submit();
    }
</script>