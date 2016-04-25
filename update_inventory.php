<?php
session_start();
require_once "database/inventory_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <table class="table_view" id="upinven_table">
            <tr>
                <td colspan="4" id="category_name">
                    <h4><?php echo $_POST["category_name"] ?></h4>
                </td>
            </tr>
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity Present</th>
                <th>Notes</th>
            </tr>
            <?php $result = InventoryTable::get_inventory($_POST["category_id"], $_SESSION["date"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><span value="<?php echo $row["name"] ?>"><?php echo $row["name"] ?></span></td>
                    <td><span value="<?php echo $row["unit"] ?>"><?php echo $row["unit"] ?></span></td>
                    <td><input type="number" class="align_center" min="0" step="any" value="<?php echo $row["quantity"] ?>" onchange=updateInventory(this)></td>
                    <td><input type="text"  value="<?php echo $row["notes"] ?>" onchange=updateInventory(this)></td>
                    <input type="hidden" value="<?php echo $row["id"] ?>">
                </tr>
            <?php endwhile ?>
        </table>
    </div>
    <input type="hidden" id="date" value="<?php echo $_SESSION["date"]; ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function updateInventory(obj) {
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemDate = document.getElementById("date").value;
        var itemId = document.getElementById("upinven_table").rows[rowIndex].children[4].value;
        var itemQuantity = document.getElementById("upinven_table").rows[rowIndex].cells[2].children[0].value;
        var itemNote = document.getElementById("upinven_table").rows[rowIndex].cells[3].children[0].value;

        $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote});
    }
</script>