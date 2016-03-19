<?php
session_start();
require_once "database/item_table.php";
require_once "database/variables_table.php";
require_once "database/base_quantity_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["userrole"] != "admin") {
    header("Location: login.php");
    exit();
}
if (isset($_POST["new_item_name"])) {
    try {
        if(!ItemTable::add_new_item($_POST["new_item_name"], $_POST["new_item_unit"])) {
            echo '<div class="error">Item already exists!</div>';
        } else {
            if (!empty($_POST["new_item_quantity"])) {
                BaseQuantityTable::set_base_quantity($_POST["new_item_name"], $_POST["new_item_quantity"]);
            }
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}
if (isset($_POST["delete_item"])) {
    ItemTable::delete_item($_POST["delete_item"]);
}
if (isset($_POST["item_name"]) OR isset($_POST["item_unit"])) {
    ItemTable::update_item_details($_POST["item_id"], $_POST["item_name"], $_POST["item_unit"]);
}
if (isset($_POST["base_sales"])) {
    VariablesTable::update_base_sales($_POST["base_sales"]);
}
if (isset($_POST["page_number"])) {
    $page_number = $_POST["page_number"];
    $limit = 10;
} else {
    $page_number = 1;
    $limit = 10;
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
    <div class="main_iframe">
    <div id="add_div_main" class="none">
        <div id="add_div" class="add_div">
        <div>
            <h4>Add New Item <hr></h4>
            <form action="edit_items.php" method="post">
            <div class="inline">
                <label for="new_item_name">Name</label>
                <input class="userinput" type="text" name="new_item_name" placeholder="Item Name" required autofocus>
            </div>
            <div class="inline">
                <label for="new_item_unit">Unit</label>
                <input class="userinput" type="text" name="new_item_unit" placeholder="Item Unit" required>
            </div>
            <div class="inline">
                <label for="new_item_quantity">Quantity</label>
                <input class="userinput" type="text" name="new_item_quantity" placeholder="Item Quantity">
            </div>
            <div class="block" id="item_add_div" >
                <input type="submit" value="Add Item" class="button button_add_drawer" id="item_add_button">
            </div>
            </form>
        </div>
        </div>
        <button id="drawer_tag" class="drawer_tag">Add</button>
    </div>
    <div class="div_fade"></div>
    <div class="div_iframe">
        <div class="pagi_main">
            <div class="pagi_div">
            <?php $total_rows = ItemTable::get_items_count();
                  $total_pages = ceil($total_rows/$limit);?>
            <input type="hidden" id="total_pages" value="<?php echo $total_pages ?>">
            <input type="hidden" id="current_page" value="<?php echo $page_number?>">
            <button class="pagi_button pb_left" onclick=changePage("back")><</button>
            <?php for ($i=1; $i <= $total_pages ; $i++): ?>
               <button class="pagi_page <?php echo $i == 1 ?  "pp_active":"" ?>" type="button" value="<?php echo $i ?>" onclick=changePage(this)><?php echo $i ?></button>
            <?php endfor ?>
            <button class="pagi_button pb_right" onclick=changePage("next")>></button>
            </div>
        </div>
        <div>
        <table class="user_table" id="table" border="1px" >
            <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Quantity for sales ($)<br/>
                    <form action="edit_items.php" method="post" >
                        <input type="number" name="base_sales" value="<?php echo VariablesTable::get_base_sales(); ?>" onchange="this.form.submit()" class="align_center">
                        <input type="hidden" name="page_number" id="page_number" value="<?php echo $page_number ?>">
                    </form>
                </th>
                <th></th>
            </tr>
            <tbody id="item_tbody">
            <?php $result = ItemTable::get_items_paginate(($page_number*$limit)-$limit, $limit); ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <form action="edit_items.php" method="post">
                    <td><input type="text" name="item_name" value="<?php echo $row["name"] ?>" onchange="this.form.submit()" class="align_center"></td>
                    <td><input type="text" name="item_unit" value="<?php echo $row["unit"] ?>" onchange="this.form.submit()" class="align_center"></td>
                    <td><input type="number" name="item_quantity" step="any" min="0" value="<?php echo $row["quantity"] ?>" onchange=quantityChange(this) class="align_center"></td>
                    <input type="hidden" name="item_id" value="<?php echo $row["id"] ?>">
                    </form>
                    <td>
                        <form action="edit_items.php" method="post" onsubmit="return confirm('delete this item?');">
                            <input type="hidden" name="delete_item" value="<?php echo $row["name"] ?>">
                            <input type="submit" value="delete" class="button" >
                        </form>
                    </td>
                </tr>
            <?php  endwhile ?>
            </tbody>
        </table>
        </div>
    </div>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function quantityChange(obj) {
        var quantity = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("table").rows[rowIndex].children[4].value;

        $.post("jq_ajax.php", {itemId: itemId, quantity: quantity});
    }
    function changePage(changeBy) {
        var limit = 10;
        var currentPage = document.getElementById("current_page").value;
        var totalPages = document.getElementById("total_pages").value;
        if(changeBy == "back" && currentPage > 1){
            currentPage--;
            loadPage(currentPage);
            $(".pagi_page.pp_active").each(function(){
                $(this).removeClass("pp_active");
                $(this).prev().addClass("pp_active");
            });
        } else if (changeBy == "next" && currentPage < totalPages) {
            currentPage++;
            loadPage(currentPage);
            $(".pagi_page.pp_active").each(function(){
                $(this).removeClass("pp_active");
                $(this).next().addClass("pp_active");
            });
        } else if (changeBy instanceof Object) {
            currentPage = changeBy.value;
            loadPage(currentPage);
            $(".pagi_page.pp_active").removeClass("pp_active");
            $(changeBy).addClass("pp_active");
        }
    }

    function loadPage(pageNumber) {
        var limit = 10;
        var offset = (pageNumber*limit)-limit;
        $.post("jq_ajax.php", {getItemsPaginate: "", offset: offset, limit: limit}, function(data, status){
            document.getElementById("item_tbody").innerHTML = data;
        });
        document.getElementById("current_page").value = pageNumber;
        document.getElementById("page_number").value = pageNumber;
    }

    $(document).ready(function() {
        $("#drawer_tag").click(function() {
            $("#add_div").slideToggle(180, "linear", function() {
                if($("#add_div").css("display") == "none") {
                    $(".div_fade").css("display", "none");
                    $("#drawer_tag").removeClass("drawer_tag_open");
                    $("#drawer_tag").text("Add");
                } else {
                    $(".div_fade").css("display", "block");
                    $("#drawer_tag").addClass("drawer_tag_open");
                    $("#drawer_tag").text("Close");
                }
            });
        });
        $(".div_fade").click(function() {
            $("#add_div").slideToggle(180, "linear");
            $(".div_fade").css("display", "none")
            $("#drawer_tag").removeClass("drawer_tag_open");
            $("#drawer_tag").text("Add");
        });
    });
</script>

