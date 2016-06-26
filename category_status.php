<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();

if (!isset($_SESSION["date"])) {
    $_SESSION["date"] = date("Y-m-d");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden">
    <?php $page = "home";
          include_once "new_nav.php" ?>
    <div class="main">
        <div class="div_category" id="home_list">
           <ul class="category_list">
            <h4><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></h4><hr>
            <?php $result = CategoryTable::get_categories($_SESSION["date"]);
                 while ($row = $result->fetch_assoc()): ?>
                 <li class="list_category_li">
                    <span><?php echo $row["name"]; ?></span>
                    <input type="hidden" id="category_id" name="category_id" value="<?php echo $row['id'] ?>">
                    <span class="item_counter" id="total"><?php echo ItemTable::get_total_items($row['id'], $_SESSION["date"]) ?></span>
                    <span class="float_right" id="<?php echo $row['name'].'_count' ?>"><?php echo ItemTable::get_updated_items_count($row['id'], $_SESSION["date"]). "/" ?> </span>
                 </li>
            <?php  endwhile?>
            </ul>
        </div>
        <div class="inventory_div">
            <div class="inventory_toolbar">
                <label class="switch float_left">
                    <input class="switch-input" type="checkbox" onclick=checkEmpty() />
                    <span class="switch-label" data-on="incomplete" data-off="All"></span>
                    <span class="switch-handle"></span>
                </label>
                <h4 id="name">Drinks</h4>
            </div>
            <div class="inventory_table">
                <table class="table_view" id="upinven_table">
                    <tr>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Quantity Present</th>
                        <th>Notes</th>
                    </tr>
                    <tbody id="item_tbody">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"]; ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function getInventory(categoryId) {
        var date = document.getElementById("session_date").value;
        $.post("jq_ajax.php", {getInventory: "", categoryId: categoryId, date: date}, function(data, status) {
            document.getElementById("item_tbody").innerHTML = data;
            if ($(".switch-input").prop("checked")) { checkEmpty(); }
        });
    }

    function updateInventory(obj) {
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemDate = document.getElementById("session_date").value;
        var itemId = document.getElementById("upinven_table").rows[rowIndex].children[4].value;
        var itemQuantity = document.getElementById("upinven_table").rows[rowIndex].cells[2].children[0].value;
        if (itemQuantity == "") {itemQuantity = 'NULL'};
        var itemNote = document.getElementById("upinven_table").rows[rowIndex].cells[3].children[0].value;

        $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote});
        if ($(".switch-input").prop("checked")) { checkEmpty(); }
        updateCount();
    }

    function checkEmpty() {
        if ($(".switch-input").prop("checked")) {
            $(".td_quantity").each(function() {
                if ($(this).children().val() >= 0 && $(this).children().val() != "") {
                    $(this).parent().hide();
                }
            });
        } else {
            $("tr").show();
        }
    }

    function updateCount() {
        var count = 0;
        $(".td_quantity").each(function() {
            if ($(this).children().val() >= "0" ) {
                count++;
            }
        var categoryName = document.getElementById("name").innerHTML;
        document.getElementById(categoryName+"_count").innerHTML = count+"/";
        });
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            getInventory($(this).children("#category_id").val());
            $(this).addClass("active");
            $("#name").html($(this).children().html());
        });

        $(".list_category_li").click(function() {
            getInventory($(this).children("#category_id").val());
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
            $("#name").html($(this).children().html());
        });
    });
</script>
