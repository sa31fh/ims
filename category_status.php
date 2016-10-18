<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/actual_sale_table.php";


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["actual_sale"])) {
    ActualSale::add_actual_sale($_POST["actual_sale"], $_SESSION["date"]);
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();
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
                <div class="toolbar_div">
                    <form class="inline" action="category_status.php" method="post">
                        <span >Actual Sales ($):</span>
                        <input class="print_expected" type="number" name="actual_sale" value="<?php echo ActualSale::get_actual_sale($_SESSION["date"]) ?>" onchange="this.form.submit()">
                    </form>
                </div>
                <h4 id="name">Drinks</h4>
            </div>
            <div class="inventory_table">
                <table class="table_view" id="upinven_table">
                    <tr>
                        <th id="heading_item">Item</th>
                        <th>Unit</th>
                        <th>Expected Quantity</th>
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
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script>
    function getInventory(categoryId) {
        var date = document.getElementById("session_date").value;
        $.post("jq_ajax.php", {getInventory: "", categoryId: categoryId, date: date}, function(data, status) {
            document.getElementById("item_tbody").innerHTML = data;
            if ($(".switch-input").prop("checked")) { checkEmpty(); }
            $(".quantity_input").each(function() {
                checkDeviation($(this)[0]);
            });
        });
    }

    function updateInventory(obj) {
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemName = document.getElementById("upinven_table").rows[rowIndex].children[0].innerHTML;
        var itemDate = document.getElementById("session_date").value;
        var itemId = document.getElementById("upinven_table").rows[rowIndex].children[5].value;
        var itemQuantity = document.getElementById("upinven_table").rows[rowIndex].cells[3].children[0].value;
        if (itemQuantity == "") {itemQuantity = 'NULL'};
        var itemNote = document.getElementById("upinven_table").rows[rowIndex].cells[4].children[0].value;

        $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote}, function(data, status) {
            if (data) {
                alertify
                    .delay(2000)
                    .success("Changes Saved");
                if ($(".switch-input").prop("checked")) { checkEmpty(); }
                updateCount();
            }
        })
        .fail(function() {
            alertify
                .maxLogItems(10)
                .delay(0)
                .closeLogOnClick(true)
                .error("Item '"+itemName+"' not saved. Click here to try again", function(event) {
                    updateInventory(obj);
                });
        });
    }
    function checkDeviation(obj) {
        var quantityPresent = obj.value;
        var row = document.getElementById("upinven_table").rows[obj.parentNode.parentNode.rowIndex];
        var itemName = row.children[0].innerHTML;
        var estimated_quantity = row.children[2].innerHTML;
        var current_deviation = (-(quantityPresent - estimated_quantity) * 100) / quantityPresent;
        var max_deviation = row.children[6].value;
        if(max_deviation < current_deviation && quantityPresent != "") {
            row.children[0].className += " warning_sign";
            alertify
                .maxLogItems(20)
                .delay(5000)
                .error("Item '"+itemName+"' below deviation limit.")
        } else {
            row.children[0].className = "item_name";
        }
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
