<?php
    include "sql_common.php";
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
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <input type="hidden" id="date" value="<?php echo $_POST["date"] ?>">
    <div>
        <table id="table">
            <caption><?php echo $_POST["category_name"] ?></caption>
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity Present</th>
                <th>Notes</th>
            </tr>
            <?php $result = get_inventory($_POST["category_id"], $_POST["date"]) ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><span value="<?php echo $row["name"] ?>"><?php echo $row["name"] ?></span></td>
                    <td><span value="<?php echo $row["unit"] ?>"><?php echo $row["unit"] ?></span></td>
                    <td><input type="number" min="0" value="<?php echo $row["quantity"] ?>" onchange=updateInventory(this)></td>
                    <td><input type="text"  value="<?php echo $row["notes"] ?>" onchange=updateInventory(this)></td>
                    <input type="hidden" value="<?php echo $row["id"] ?>">
                </tr>
            <?php endwhile ?>
        </table>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function updateInventory(obj){
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var date = document.getElementById("date").value;
        var item_id = document.getElementById("table").rows[rowIndex].children[4].value;
        var item_quantity = document.getElementById("table").rows[rowIndex].cells[2].children[0].value;
        var item_notes = document.getElementById("table").rows[rowIndex].cells[3].children[0].value;

        $(function(){
            $.post("sql_common.php", {itId: item_id, itDate: date, itQuan: item_quantity, itNote: item_notes});
        });
    }
</script>