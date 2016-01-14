<?php
    include "sql_common.php";
    session_start(); 
    if (!isset($_SESSION["username"])) {
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
    <?php include_once "new_nav.php" ?>

    <div>
        <table id="table" border="1px" >
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity for sales ($)<br/>
                    <form action="edit_items.php" method="post" >
                        <input type="number" name="base_sales" value="<?php echo get_base_sales(); ?>" onchange="this.form.submit()"></form>
                </th>
            </tr>
            <?php $result = get_items(); ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <form action="edit_items.php" method="post">
                    <td><input type="text" id="item_name" name="item_name" value="<?php echo $row["name"] ?>" onchange="this.form.submit()"></td>
                    <td><input type="text" name="item_unit" value="<?php echo $row["unit"] ?>" onchange="this.form.submit()"></td>
                    <td><input type="number" id="item_quantity" name="item_quantity" value="<?php echo $row["quantity"] ?>" onchange=quantityChange(this)></td>
                    <input type="hidden" id="item_id" name="item_id" value="<?php echo $row["id"] ?>">
                    </form>
                </tr>
            <?php  endwhile ?>

        </table>
    </div>

    <div>
        <div><h4>Add New Item</h4></div>
        <div class="user_view">
            <span>Name</span><br>
            <span>Unit</span>
        </div>
        <div class="user_view">
            <form action="edit_items.php" method="post">
                <input type="text" name="new_item_name" required><br/>
                <input type="text" name="new_item_unit" required><br/>
                <input type="submit" value="Add Item" class="button">
            </form>
        </div>
    </div>

    <div>
        <div><h4>Delete Item</h4></div>
        <form action="edit_items.php" method="post">
            <select name="delete_item" class="none">
                <?php $result = get_items(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value= "<?php echo $row["name"] ?> "> <?php echo $row["name"] ?> </option>
                <?php endwhile ?>
            </select >
            <input type="submit" value="Delete" class="button">
        </form>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function quantityChange(obj){
        var quantity = obj.value;
        var rowindex = obj.parentNode.parentNode.rowIndex;
        var item_id = document.getElementById("table").rows[rowindex].children[4].value;

        $(function(){
            $.post("sql_common.php", {item_id: item_id, quantity: quantity});
        });
    }
</script>

