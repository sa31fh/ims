<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/sales_table.php";
require_once "database/cash_closing_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["actual_sale"])) {
    SalesTable::add_actual_sale($_POST["actual_sale"], $_SESSION["date"]);
}
if(isset($_POST["new_date"])) {
    $_SESSION["date"] = $_POST["new_date"];
    $_SESSION["date_check"] = "checked";
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();
$todays_sales = SalesTable::get_actual_sale($_SESSION["date"]);
$readonly = $_SESSION["date"] <= date('Y-m-d', strtotime("-".$_SESSION["history_limit"])) ? "readonly" : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta id="vp" name="viewport" content="width=device-width">
    <script>
        if (screen.width < 700)
        {
            var vp = document.getElementById('vp');
            vp.setAttribute('content','width=780');
        }
    </script>
    <title>Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden">

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
                    <div id="dollar_sign"><?php echo $todays_sales == "" ? "" : "$";?></div>
                    <div id="amount"><?php echo $todays_sales == "" ? "-" : $todays_sales;?></div>
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
                <div class="toolbar_div flex_1">
                    <h4 id="name"></h4>
                </div>
                <div class="toolbar_div search_div">
                    <input class="search_bar" id="search_bar" type="search" placeholder="search" oninput=searchBar(this)>
                </div>
                <div class="toolbar_div" id="div_pp">
                    <a href="print_preview.php" class="fa-print pp_button">Print Preview</a>
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
                    <tbody class="font_roboto" id="item_tbody"></tbody>
                    <tbody class="font_roboto" id="search_tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="div_popup_back" id="date_check_popup">
        <div id="date_check_holder"></div>
    </div>

    <div class="div_popup_back" id="sales_popup">
        <div class="div_popup popup_todays_sales">
            <div class="popup_titlebar">
                <span>Calculate Todays Sales</span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <div class="div_sales">
                <?php $result = CashClosingTable::get_row_data($_SESSION["date"]) ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="flex_row div_cell">
                        <div  class="flex_1">
                            <span><?php echo $row["name"] ?></span>
                        </div>
                        <input class="flex_1 row_amount" type="number" value="<?php echo $row["quantity"] ?>"
                                onchange="updateTodaysSales(this)" placeholder="enter amount">
                        <input type="hidden" id="type" value="<?php echo $row["type"] ?>">
                        <input type="hidden" id="row_id" value="<?php echo $row["id"] ?>">
                    </div>
                <?php endwhile ?>
            </div>
            <div class="flex_row total_bar">
                <span class="flex_1">Total</span>
                <span>$</span>
                <span class="flex_1" id="total_sales"></span>
            </div>
             <div class="toolbar_print" id="sales_tabs">
                <div class="toolbar_div option " id="calculated">Calculated</div>
                <div class="toolbar_div option" id="custom">Custom</div>
            </div>
            <div class="flex_row todays_sales">
                <span class="flex_1">Todays Sales</span>
                <span>$</span>
                <input class="flex_1" type="number" id="total_input" placeholder="enter amount" value="<?php echo $todays_sales == "" ? "-" : $todays_sales;?>">
            </div>
            <div class="div_save">
                <button class="button" onclick=saveSales()>Save</button>
            </div>
        </div>
    </div>

    <form action="category_status.php" method="post" id="sales_form">
        <input type="hidden" name="actual_sale" id="actual_sale">
    </form>

    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"]; ?>">
    <input type="hidden" id="date_check" value="<?php echo isset($_SESSION["date_check"]); ?>">
     <?php $page = "home";
           include_once "new_nav.php"; ?>
</body>
</html>

<script type="text/javascript" src="jq/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="jq/jquery-ui.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<?php if ($_SESSION["date"] <= date('Y-m-d', strtotime("-".$_SESSION["history_limit"]))): ?>
    <script> $("input:not(#search_bar)").prop("readonly", true); </script>
<?php endif ?>
<script>
    function getInventory(categoryId , callBack) {
        var date = document.getElementById("session_date").value;
        if ($("#item_tbody").html() == "") {
            $.post("jq_ajax.php", {getInventory: "", categoryId: categoryId, date: date}, function(data, status) {
                document.getElementById("item_tbody").innerHTML = data;
                $("#item_tbody").children().hide();
                $("#item_tbody").children().each(function() {
                    if ($(this).find("#cat_id").val() == categoryId) {
                        $(this).show();
                    }
                });
            });
        } else {
            $("#item_tbody").children().hide();
            $("#item_tbody").children().each(function() {
                if ($(this).find("#cat_id").val() == categoryId) {
                    $(this).show();
                }
            });
        }
            typeof callBack === "function" ? callBack() : "";
            if ($(".switch-input").prop("checked")) { checkEmpty(); }
    }

    function updateInventory(obj) {
        var row = document.getElementById("upinven_table").rows[obj.parentNode.parentNode.rowIndex];
        var itemQuantity = row.cells[3].children[0].value;
        if (itemQuantity < 0) {
            row.cells[3].children[0].value = "";
        } else {
            var itemName = row.children[0].innerHTML;
            var itemDate = document.getElementById("session_date").value;
            var itemId = row.children[5].value;
            itemQuantity = itemQuantity == "" ? 'NULL' : itemQuantity;
            var itemNote = row.cells[4].children[0].value;
            $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote}, function(data, status) {
                if (data) {
                    alertify
                        .delay(2000)
                        .success("Changes Saved");
                    if ($("#name").html() != "search result") {
                        if ($(".switch-input").prop("checked")) { checkEmpty(); }
                    }
                    updateCount(row.children[7].value);
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
    }

    function updateTodaysSales(obj) {
        var rowId = $(obj).parent().find("#row_id").val();
        var date = $("#session_date").val();
        var note = 'NULL';
        var quantity = obj.value == "" ? 'NULL' : obj.value;
        calculateTotal();
        $.post("jq_ajax.php", {updateCashClosingRow: "", rowId: rowId, date: date, quantity: quantity, note: note});
    }

    function saveSales() {
        var sales = $("#total_input").val() == "" ? 'NULL' : $("#total_input").val();
        $.post("jq_ajax.php",{calcExpected: "", todaysSale: sales});
        $("#actual_sale").val(sales);
        $("#sales_form").submit();
    }

    function calculateTotal() {
        var total_sales = 0;
        $(".div_sales .row_amount").each(function() {
            var quantity = $(this).val();
            var type = $(this).next().val();
            if (quantity != "") {
                quantity = parseFloat(quantity);
                if (type == 0) {
                    total_sales = total_sales + quantity;
                } else if (type == 1) {
                    total_sales = total_sales - quantity;
                }
                total_sales = +total_sales.toFixed(2);
            }
        });
        $("#total_sales").html(total_sales);
        if ($("#sales_tabs .selected").html() == "Calculated") {
            $("#total_input").val(total_sales);
        }
    }

    function checkDeviation(obj, message, icon) {
        var quantityPresent = obj.value;
        var row = document.getElementById("upinven_table").rows[obj.parentNode.parentNode.rowIndex];
        var itemId = row.children[5].value;
        var date = $("#session_date").val();
        if (quantityPresent != "") {
            var itemName = row.children[0].innerHTML;
            var estimated_quantity = row.children[2].innerHTML;
            if (quantityPresent > 0) {
                var current_deviation = (Math.abs(quantityPresent - estimated_quantity) * 100) / quantityPresent;
            } else {
                var current_deviation = (Math.abs(quantityPresent - estimated_quantity) * 100) / 1;
            }
            var max_deviation = row.children[6].value;
            if(max_deviation < current_deviation) {
                if (icon) {
                    row.children[0].className += " warning_sign";
                }
                if (message) {
                    alertify
                        .maxLogItems(20)
                        .delay(3000)
                        .error("Item '"+itemName+"' is outside deviation range.")
                }
                $.post("jq_ajax.php", {updateDeviation: "", itemId: itemId, date: date, deviation: 1});
            } else {
                row.children[0].className = "item_name entypo-attention";
                $.post("jq_ajax.php", {updateDeviation: "", itemId: itemId, date: date, deviation: 0});
            }
        } else {
                row.children[0].className = "item_name entypo-attention";
                $.post("jq_ajax.php", {updateDeviation: "", itemId: itemId, date: date, deviation: 0});
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
            getInventory($(".list_category_li.active").find("#category_id").val());
        }
    }

    function updateCount(id) {
        var count = 0;
        var categoryName = "";
        $(".list_category_li").each(function() {
            if ($(this).find("#category_id").val() == id) {
                categoryName = $(this).find("#category_name").html();
            }
        });
        $("#item_tbody #cat_id").each(function() {
            if ($(this).val() == id) {
                if ($(this).parent().find(".td_quantity").children().val() >= "0") {
                    count++;
                }
            }
        });
        document.getElementById(categoryName+"_count").innerHTML = count;
    }

    (function(){
        if ($("#date_check").val() == "") {
            $("#date_check_popup").css("display", "block");
            $(".main").addClass("blur");
            var date_check_holder = document.getElementById("date_check_holder");
            var date_check_title = document.createElement("div");
            var date_check_div = document.createElement("div");
            date_check_title.setAttribute("id", "date_check_title");
            date_check_title.innerHTML = "select date";
            date_check_div.setAttribute("id", "date_check_div");
            date_check_holder.appendChild(date_check_title);
            date_check_holder.appendChild(date_check_div);

            $("#date_check_div").datepicker({
                dateFormat: "yy-mm-dd",
                defaultDate: $("#cal_date").val(),
                dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
                currentText: "close",
                prevText: "previous",
                onSelect: function(dateText) {
                    $("#cal_date").val(dateText);
                    $("#cal_form").submit();
                }
            });
        }
    })();

    function searchBar(obj) {
        if (obj.value != "") {
            var searchWord = new RegExp(obj.value, "i");
            $("#item_tbody").children().hide();
            $("#item_tbody").find(".item_name").each(function() {
                var val = $(this).html();
                if (val.search(searchWord) > -1) {
                    $(this).parent().show();
                }
            });
            $("#name").html("search result");
        } else {
            getInventory($(".list_category_li.active").find("#category_id").val(), function() {
                $(".search_bar").val("");
                $("#name").html($(".list_category_li.active").find("#category_name").html());
            });
        }
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            getInventory($(this).find("#category_id").val());
            $(this).addClass("active");
            $("#name").html($(this).find("#category_name").html());
        });

        $(".list_category_li").click(function() {
            getInventory($(this).find("#category_id").val(), function() {
                $(".search_bar").val("");
            });
                $(".list_category_li").removeClass("active");
                $(this).addClass("active");
                $("#name").html($(this).find("#category_name").html());
        });

        $(".time_div").click(function() {
            $("#div_cal .ui-datepicker").css("display", "block");
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
            if ($("#date_check").val() != "") {
                if(!$(event.target).closest('.time_div').length && !$(event.target).is("a, span")) {
                    if($('.ui-datepicker').is(":visible")) {
                        $('#div_cal .ui-datepicker').css("display", "none");
                    }
                }
            }
        });

        $(".div_actual_sales").click(function() {
            calculateTotal();
            $("#sales_popup").css("display", "block");
            $("#sales_tabs .toolbar_div").removeClass("selected");
            if ($("#total_input").val() == $("#total_sales").html() || $("#total_input").val() == "" ) {
                $("#calculated").addClass("selected");
                $("#total_input").prop("readonly", true)
                $("#total_input").val($("#total_sales").html());
            } else {
                $("#custom").addClass("selected");
                $("#total_input").prop("readonly", false)
                $("#total_input").val($("#right #amount").html());
            }
        });

        $("#sales_tabs .toolbar_div").click(function() {
            $("#sales_tabs .toolbar_div").removeClass("selected");
            $(this).addClass("selected");

            if ($(this).html() == "Calculated") {
                $("#total_input").val($("#total_sales").html());
                $("#total_input").prop("readonly", true);
            } else {
                $("#total_input").prop("readonly", false);
                $("#total_input").val($("#right #amount").html());
            }
        });

        $("input[type=number]").on("keypress" , function(event) {
            if ((event.which < 46 || event.which > 57) && (event.which != 8 &&
                event.which != 0 && event.which != 13)) {
                event.preventDefault();
            }
        });

        $("#popup_close").click(function() {
            $("#sales_popup").css("display", "none");
            $("#total_input").val($("#right #amount").html());
        });
    });
</script>
