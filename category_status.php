<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/sales_table.php";


if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["actual_sale"])) {
    SalesTable::add_actual_sale($_POST["actual_sale"], $_SESSION["date"]);
}
if(isset($_POST["new_date"])) {
    $_SESSION["date"] = $_POST["new_date"];
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
    <meta id="vp" name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        if (screen.width < 700)
        {
            var vp = document.getElementById('vp');
            vp.setAttribute('content','width=800');
        }
    </script>
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden">
    <?php $page = "home";
          include_once "new_nav.php"; ?>
    <div class="main">
        <div class="div_category " id="home_list">
            <?php $current_date = strtotime($_SESSION["date"]); ?>
            <div class="time_div font_open_sans">
                <div class="time_div_container">
                    <span class="date_span fa-calendar" aria-hidden="true"><?php echo date("d", $current_date); ?></span>
                </div>
                <div class="time_div_container">
                    <span class="year_span"><?php echo date("Y", $current_date); ?></span>
                    <span class="month_span"><?php echo date("F", $current_date); ?></span>
                </div>
                    <span class="day_span"><?php echo date("l", $current_date); ?></span>
                <div id="div_cal"></div>
                <form action="category_status.php" method="post" id="cal_form">
                    <input type="hidden" id="cal_date" name="new_date" value="<?php echo $_SESSION["date"] ?>">
                </form>
            </div>
            <div class="div_actual_sales">
                <div id="left">
                    <span >Todays Sales</span>
                </div>
                <div id="right">
                    <form  class="inline" action="category_status.php" method="post">
                        <span>$</span>
                        <input type="number" name="actual_sale" value="<?php echo SalesTable::get_actual_sale($_SESSION["date"]) ?>" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
            <ul class="category_list home_category_list font_roboto" >
            <?php $result = CategoryTable::get_categories($_SESSION["date"]);
                 while ($row = $result->fetch_assoc()): ?>
                 <li class="list_category_li home_category_list_li">
                    <div class="list_li_div_left">
                        <span id="category_name"><?php echo $row["name"]; ?></span>
                    </div>
                    <div class="list_li_div_right">
                        <span class="count_span_filled" id="<?php echo $row['name'].'_count' ?>">
                        <?php echo ItemTable::get_updated_items_count($row['id'], $_SESSION["date"]) ?></span>
                        <span class="count_span_total"><?php echo ItemTable::get_total_items($row['id'], $_SESSION['date']) ?></span>
                        <input type="hidden" id="category_id" name="category_id" value="<?php echo $row['id'] ?>">
                    </div>
                 </li>
            <?php  endwhile?>
            </ul>
        </div>
        <div class="inventory_div">
            <div class="inventory_toolbar font_roboto">
                <div class="toolbar_div" id="div_switch">
                   <label class="switch float_left">
                       <input class="switch-input" type="checkbox" onclick=checkEmpty() />
                       <span class="switch-label" data-on="incomplete" data-off="All"></span>
                       <span class="switch-handle"></span>
                   </label>
                </div>
                <div class="toolbar_div">
                    <h4 id="name">Drinks</h4>
                </div>
                <div class="toolbar_div" id="div_pp">
                    <a href="print_preview.php" class="fa-print print_preview">Print Preview</a>
                </div>
            </div>
            <div class="inventory_table">
                <table class="table_view" id="upinven_table">
                    <tr  class="font_roboto">
                        <th id="heading_item">Item</th>
                        <th>Unit</th>
                        <th>Expected Quantity</th>
                        <th>Actual Quantity</th>
                        <th>Notes</th>
                    </tr>
                    <tbody class="font_open_sans" id="item_tbody">
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
<script
      src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
      integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
      crossorigin="anonymous"></script>
<script>
    function getInventory(categoryId) {
        var date = document.getElementById("session_date").value;
        $.post("jq_ajax.php", {getInventory: "", categoryId: categoryId, date: date}, function(data, status) {
            document.getElementById("item_tbody").innerHTML = data;
            if ($(".switch-input").prop("checked")) { checkEmpty(); }
            $(".quantity_input").each(function() {
                checkDeviation($(this)[0], false, true);
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
    function checkDeviation(obj, message, icon) {
        var quantityPresent = obj.value;
        var row = document.getElementById("upinven_table").rows[obj.parentNode.parentNode.rowIndex];
        var itemName = row.children[0].innerHTML;
        var estimated_quantity = row.children[2].innerHTML;
        if (quantityPresent > 0) {
            var current_deviation = (Math.abs(quantityPresent - estimated_quantity) * 100) / quantityPresent;
        } else {
            var current_deviation = (Math.abs(quantityPresent - estimated_quantity) * 100) / 1;
        }
        var max_deviation = row.children[6].value;
        if(max_deviation < current_deviation && quantityPresent != "") {
            if (icon) {
                row.children[0].className += " warning_sign";
            }
            if (message) {
                alertify
                    .maxLogItems(20)
                    .delay(5000)
                    .error("Item '"+itemName+"' is outside deviation range.")
            }
        } else {
            row.children[0].className = "item_name entypo-attention";
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
        });

        var categoryName = document.getElementById("name").innerHTML;
        document.getElementById(categoryName+"_count").innerHTML = count;
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            getInventory($(this).find("#category_id").val());
            $(this).addClass("active");
            $("#name").html($(this).find("#category_name").html());
        });

        $(".list_category_li").click(function() {
            getInventory($(this).find("#category_id").val());
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
            $("#name").html($(this).find("#category_name").html());
        });

        $(".time_div").click(function() {
            $(".ui-datepicker").css("display", "block");
            $("#div_cal").datepicker({
                dateFormat: "yy-mm-dd",
                defaultDate: $("#cal_date").val(),
                dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
                showButtonPanel: true,
                currentText: "close",
                prevText: "previous",
                onSelect: function(dateText) {
                    $(".ui-datepicker").css("display", "none");
                    $("#cal_date").val(dateText);
                    $("#cal_form").submit();
                }
            });
        });

        $(document).click(function(event) {
            if(!$(event.target).closest('.time_div').length && !$(event.target).is("a, span") ) {
                if($('.ui-datepicker').is(":visible")) {
                    $('.ui-datepicker').css("display", "none");
                }
            }
        });
    });
</script>
