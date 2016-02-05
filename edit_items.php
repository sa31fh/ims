<?php
    include "sql_common.php";
    session_start(); 
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if ($_SESSION["userrole"] != "admin") {
        header("Location: login.php");
        exit();
    }
    if (isset($_POST["new_item_name"])) {
        add_new_item($_POST["new_item_name"], $_POST["new_item_unit"]);
    }
    if (isset($_POST["delete_item"])) {
        delete_item($_POST["delete_item"]);
    }
    if (isset($_POST["item_name"]) OR isset($_POST["item_unit"])) {
        update_item_details($_POST["item_id"], $_POST["item_name"], $_POST["item_unit"]);
    }
    if (isset($_POST["base_sales"])) {
        update_base_sales($_POST["base_sales"]);
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
    <div>
        <table class="user_table" id="table" border="1px" >
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity for sales ($)<br/>
                    <form action="edit_items.php" method="post" >
                        <input type="number" name="base_sales" value="<?php echo get_base_sales(); ?>" onchange="this.form.submit()" class="align_center"></form>
                </th>
                <th></th>
            </tr>
            <?php $result = get_items(); ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <form action="edit_items.php" method="post">
                    <td><input type="text" name="item_name" value="<?php echo $row["name"] ?>" onchange="this.form.submit()" class="align_center"></td>
                    <td><input type="text" name="item_unit" value="<?php echo $row["unit"] ?>" onchange="this.form.submit()" class="align_center"></td>
                    <td><input type="number" name="item_quantity" value="<?php echo $row["quantity"] ?>" onchange=quantityChange(this) class="align_center"></td>
                    <input type="hidden" name="item_id" value="<?php echo $row["id"] ?>">
                    </form>
                    <td>
                        <form action="edit_items.php" method="post">
                            <input type="hidden" name="delete_item" value="<?php echo $row["name"] ?>">
                            <input type="submit" value="delete" class="button">
                        </form>
                    </td>
                </tr>
            <?php  endwhile ?>
        </table>
    </div>

    <div class="user_add_div">
        <h4>Add New Item</h4>
        <form action="edit_items.php" method="post">
            <input class="userinput" type="text" name="new_item_name" placeholder="Item Name" required><br/>
            <input class="userinput" type="text" name="new_item_unit" placeholder="Item Unit" required><br/>
            <input type="submit" value="Add Item" class="button">
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function quantityChange(obj){
        var quantity = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("table").rows[rowIndex].children[4].value;

        $(function(){
            $.post("jq_ajax.php", {itemId: itemId, quantity: quantity});
        });
    }
</script>

