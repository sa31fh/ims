<?php
session_start();
require_once "database/timeslot_table.php";
require_once "database/sales_table.php";
require_once "database/invoice_table.php";
require_once "database/sales_table.php";
require_once "database/catering_order_table.php";
require_once "database/bulk_order_data_table.php";
require_once "database/daily_order_data_table.php";
require_once "mpdf/vendor/autoload.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (isset($_POST["expected_sales"])) {
    $sales = empty($_POST["expected_sales"]) ? 'NULL' : $_POST["expected_sales"];
    SalesTable::add_expected_sale($sales, $_SESSION["date"]);
}
if (isset($_POST["table_data"])) {
    $mpdf = new mPDF("", "A4", 0, 'roboto', 0, 0, 0, 0, 0, 0);
    $stylesheet = file_get_contents("css/pdf_styles.css");
    $mpdf->useSubstitutions=false;
    $mpdf->simpleTables = true;
    $mpdf->WriteHtml($stylesheet, 1);
    $mpdf->WriteHtml($_POST["table_data"], 2);
    $mpdf->Output($_POST["table_name"]." - ".$_POST["table_date"].".pdf", "D");
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();

$future_date = date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("2 day"))), 'Y-m-d');
$result = InvoiceTable::get_tracked($_SESSION["date"])->fetch_assoc();
$inventory_invoice = count($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Preview</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="font_roboto">
    <div class="main overflow_hidden">
        <div class="sidenav">
            <div id="heading"><h4>Print Preview</h4></div>
            <ul class="side_nav print_preview">
                <li id="order_li">
                    <div class="heading" id="inventory">
                        <span>Inventory</span>
                    </div>
                </li>
                <li id="daily_order">
                    <a>
                        <span id="left">Daily order</span>
                        <span id="right" class="warning fa-star"></span>
                        <div class="status_view">
                            <span class="flex_1">required items</span>
                            <span id="item_num">-</span>
                        </div>
                    </a>
                </li>
                <li id="bulk_order">
                    <a>
                        <span id="left">bulk order</span>
                        <input type="hidden" id="bulk_invoice" value="0">
                    </a>
                </li>
                <!-- <li id="order_li">
                    <div class="heading" id="catering_option">
                        <span>Catering</span>
                    </div>
                </li>
                <li id="catering_order">
                    <a>
                        <span id="left">Catering Orders</span>
                        <span id="right"><?php echo CateringOrderTable::get_order_count($_SESSION["date"], $future_date); ?></span>
                    </a>
                </li> -->
            </ul>
        </div>
        <div class="main_top_side">
            <div class="toolbar_print" id="pp_toolbar">
                <div class="toolbar_div">
                    <a href="category_status.php" class="option" id="back">back</a>
                </div>
                <div class="toolbar_div">
                    <label class="switch" id="toolbar_toggle">
                        <input class="switch-input" type="checkbox" onclick=checkRequired() />
                        <span class="switch-label" data-on="Required" data-off="All"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
                <div class="toolbar_div">
                    <a id="print_share" class="option" onclick=sendPrint()>Share</a>
                </div>
                <div class="toolbar_div">
                    <a id="print_pdf" class="option" onclick=printPdf()>PDF</a>
                </div>
                <div class="toolbar_div">
                    <a id="print_all" class="fa-print option" onclick=printAll()>Print All</a>
                </div>
                <div class="toolbar_div invoice_send" id="div_invoice_send"onclick="trackInvoice()">
                    Send Invoice
                </div>
            </div>

        <?php if ($_SESSION["userrole"] == "admin"): ?>
            <div class="div_expected" id="daily_expected">
                 <?php
                    $result = DailyOrderDataTable::get_dates($_SESSION["date"]);
                    if (mysqli_num_rows($result) > 0) {
                        $dates = $result->fetch_assoc();
                        $qp_date = date_create($dates["qp_date"]);
                        $qp_date_text = date_format($qp_date, "j M Y");
                    } else{
                        $qp_date =  date_create($_SESSION["date"]);
                        $qp_date_text = date_format($qp_date, "j M Y");
                    }
                ?>
                <div class="daily_qp_date">
                    <span id="heading">Quantity Present Date</span>
                    <span ><?php echo date_format($qp_date, "j M Y") ?></span>
                    <input type="hidden" id="daily_qp_date" value="<?php echo date_format($qp_date, "Y-m-d") ?>">
                    <div class="div_cal"></div>
                </div>
                <div class="left">
                    <span id="heading">Todays sales</span>
                    <span id="amount">
                    <?php $todays_sales = SalesTable::get_actual_sale($_SESSION["date"]);
                    echo is_null($todays_sales) ? "-" :  "$ ".$todays_sales;?>
                    </span>
                </div>
                <div class="center daily_expected_sales">
                    <span id="heading">Expected Sales</span>
                    <form action="print_preview.php" method="post" id="expected_form">
                        <span id="icon">$</span>
                        <input class="print_expected" type="number" name="expected_sales" value="<?php echo SalesTable::get_expected_sale($_SESSION['date']) ?>" onchange=updateExpectedSales(this)>
                    </form>
                </div>
                <div class="right">
                    <?php $date =  date_sub(date_create($_SESSION["date"]), date_interval_create_from_date_string("6 days"));?>
                    <span id="heading"><?php echo "last ".date_format($date, "l")."s sales" ?></span>
                    <span id="amount">
                    <?php $last_week = date_format($date, "Y-m-d");
                    $sales = SalesTable::get_actual_sale($last_week);
                    echo is_null($sales) ? "-" : "$ ".$sales;?>
                    </span>
                </div>

            </div>
            <div class="div_expected" id="bulk_expected">
                <?php
                $result = BulkOrderDataTable::get_bulk_dates($_SESSION["date"]);
                if (mysqli_num_rows($result) > 0) {
                    $bulk_dates = $result->fetch_assoc();
                    $date_from = date_create($bulk_dates["date_start"]);
                    $date_to = date_create($bulk_dates["date_end"]);
                    $date_from_text = date_format($date_from, "j M Y");
                    $date_to_text = date_format($date_to, "j M Y");
                    $qp_date = date_create($bulk_dates["qp_date"]);
                } else{
                    $date_from =  date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"));
                    $date_to =  date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("3 day"));
                    $qp_date = $date_from;
                    $date_from_text = "Enter Date";
                    $date_to_text = "Enter Date";
                }
                ?>
                <div class="center bulk_qp_date">
                    <span id="heading">Quantity Present Date</span>
                    <span ><?php echo date_format($qp_date, "j M Y") ?></span>
                    <input type="hidden" id="bulk_qp_date" value="<?php echo date_format($qp_date, "Y-m-d") ?>">
                    <div class="div_cal"></div>
                </div>
                <div class="left">
                    <span id="heading">Date From</span>
                    <span id="date_from"><?php echo $date_from_text ?></span>
                    <input type="hidden" id="date_from_val" value="<?php echo date_format($date_from, "Y-m-d") ?>">
                    <div class="div_cal"></div>
                </div>
                <div class="center" id="bulk_expected_view">
                    <span id="heading">Date To</span>
                    <span id="date_to"><?php echo $date_to_text ?></span>
                    <input type="hidden" id="date_to_val" value="<?php echo date_format($date_to, "Y-m-d") ?>">
                    <div class="div_cal"></div>
                </div>
                <div class="right">
                    <span id="heading">Expected Sales</span>
                    <span id="icon">$</span>
                    <span id="total_expected">-</span>
                </div>
            </div>
        <?php endif ?>

            <div class="div_table" id="div_table">
                <div class="div_tab">
                    <div class="div_child" id="inventory_tabs">
                        <div class="div_left_tabs">
                            <ul class="tab_ul">
                                <li class="tab_li"><span id="day_tab" onclick=getTab(this)><?php echo "Full Day" ?></span></li>
                                <input type="hidden" id="inventory_invoice" value="<?php echo $inventory_invoice ?>" >
                            </ul>
                        </div>
                        <div class="div_right_tabs">
                            <ul class="tab_ul inline" id="timeslot_ul">
                            <?php $result = TimeslotTable::get_timeslots(); ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="tab_div" >
                                    <li class="tab_li" ><span onclick=getTab(this)><?php echo $row["name"] ?></span></li>
                                </div>
                            <?php endwhile ?>
                            </ul>
                        </div>
                    </div>
                    <div class="div_child" id="catering_tabs">
                        <ul class="tab_ul inline" id="timeslot_ul">
                            <?php $result = CateringOrderTable::get_orders_by_date($_SESSION["date"], $future_date); ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="tab_div"">
                                    <li class="tab_li" id="<?php echo $row['id'] ?>" onclick=getOrderTab(this)>
                                        <span><?php echo $row["name"] ?></span>
                                        <input type="hidden" id="order_date" value="<?php echo date('D, jS M Y', strtotime($row["date_delivery"])) ?>">
                                        <input type="hidden" id="order_invoice" value="<?php echo $row['date_invoice']?>">
                                        <input type="hidden" id="order_note" value="<?php echo $row["notes"] ?>">
                                    </li>
                                </div>
                            <?php endwhile ?>
                        </ul>
                    </div>
                </div>
                <div id="div_print_table">
                    <table class="table_view" id="print">
                        <tr id="print_date" class="row">
                            <th colspan="7">
                                <div id="table_date_heading"></div>
                                <span id="table_date_span"><?php echo date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"))), 'D, jS M Y'); ?></span>
                                <div class="print_table_date"><?php echo "created on ".date('jS M Y', strtotime($_SESSION["date"])); ?></div>
                            </th>
                        </tr>
                    </table>
                    <div id="order_note_div">
                        <span class="note_heading entypo-pencil">Special Instructions</span>
                        <textarea  id="note_text" class="note_text" onchange=updateOrderNote(this) placeholder="Add Special Instructions to Order"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="div_popup_back" id="share_popup">
        <div class="div_popup popup_share">
            <div class="popup_titlebar">
                <span>New Message</span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <iframe id="popup_frame" name="popup_frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <div class="div_popup_back" id="expected_popup">
        <div class="div_popup popup_todays_sales">
            <div class="popup_titlebar">
                <span>Calculate Expected Sales</span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <div class="div_sales" id="div_total_expected"></div>
            <div class="flex_row total_bar">
                <span class="flex_1">Total</span>
                <span>$</span>
                <span class="flex_1" id="total_sales"></span>
            </div>
            <div class="div_save">
                <button class="button" id="bulk_expected_done">Done</button>
            </div>
        </div>
    </div>

    <div class="div_popup_back" id="bulk_custom_popup">
        <div class="div_popup popup_todays_sales">
            <div class="popup_titlebar">
                <span>Enter Custom Quantity Values</span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <div class="div_sales" id="div_total_custom"></div>
            <div class="flex_row total_bar">
                <span class="flex_2">Total Quantity</span>
                <span class="flex_1" id="total_sales"></span>
                <div class="divider"></div>
                <span class="flex_1">Total Cost</span>
                <span class="flex_1" id="total_cost"></span>
            </div>
            <div class="div_save">
                <button class="button" id="custom_quantity_done">Done</button>
            </div>
            <input type="hidden" id="tbody_index">
            <input type="hidden" id="row_index">
        </div>
    </div>

    <form action="invoice.php" method="post" id="invoice_form">
        <input type="hidden" name="created_date" id="session_date" value="<?php echo $_SESSION["date"] ?>">
    </form>
    <form action="invoice.php" method="post" id="bulk_invoice_form">
        <input type="hidden" name="bulk_created_date" value="<?php echo $_SESSION["date"] ?>">
    </form>
    <form action="invoice.php" method="post" id="catering_invoice_form">
        <input type="hidden" name="catering_created_date" value="<?php echo $_SESSION["date"] ?>">
    </form>
    <input type="hidden" id="formatted_date" value="<?php echo date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"))), 'D, jS M Y'); ?>">

    <form action="compose_messages.php" method="post" id="print_form" target="popup_frame">
        <input type="hidden" id="print_table_date" name="print_table_date">
        <input type="hidden" id="print_table_name" name="print_table_name">
        <input type="hidden" id="new_print_data" name="new_print_data">
    </form>

    <form action="print_preview.php" method="post" id="test_form" name="test_form">
        <input type="hidden" id="table_data" name="table_data">
        <input type="hidden" id="table_date" name="table_date">
        <input type="hidden" id="table_name" name="table_name">
    </form>
</body>
</html>

<script type="text/javascript" src="jq/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="jq/jquery-ui.min.js"></script>
<?php if ($_SESSION["date"] <= date('Y-m-d', strtotime("-".$_SESSION["history_limit"]))): ?>
    <script> $("input").prop("readonly", true); </script>
<?php endif ?>
<script>
    function getTab(tabName) {
        var timeSlotName = tabName.innerHTML;
        var date = document.getElementById("session_date").value;
        var qpDate = $("#daily_qp_date").val()
        if (timeSlotName == "Full Day") {
            $.post("jq_ajax.php", {getPrintPreview: "", date: date, qpDate: qpDate}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                $("#table_date_span").html($("#formatted_date").val());
                $("#table_date_span").css("color", "initial");
                $(".print_table_date").css("display", "block");
                checkRequired();
                if ($(".span_qc").length > 0) {
                    $("#right.warning").css("opacity", "1");
                checkRequiredItems();
                    if (!$(".status_view").hasClass("visible")) {
                        $(".status_view").addClass("visible");
                        $(".status_view").one("transitionend", function(){
                            $(this).css("z-index", "1");
                        });
                    }
                }
                $(".div_required").each(function() {
                    if ($(this).find(".span_qc").val() != "") {
                        $(this).find(".tab").trigger("click");
                    }
                });
            });
        } else {
            $.post("jq_ajax.php", {getPrintPreviewTimeslots: "", date: date, timeSlotName: timeSlotName}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                $(".print_table_date").css("display", "block");
                $("#table_date_span").html($("#formatted_date").val());
                checkRequired();
            });
        }
    }

    function getOrderTab(obj) {
        var orderId = $(obj).attr("id"); 
       $.post("jq_ajax.php", {getCateringItemTable: "", orderId: orderId}, function(data, status) {
            $("#table_date_span").html($(obj).find("#order_date").val());
            $(".print_tbody").remove();
            $("#print").append(data);
            checkRequired();
        });
    }

    function updateExpectedSales(obj) {
        if (obj.value < 0) {
            obj.value = "";
            $.post("jq_ajax.php", {calcQuantityRequired : "", expectedSales: obj.value});
        } else {
            $.post("jq_ajax.php", {calcQuantityRequired : "", expectedSales: obj.value});
            obj.parentNode.submit();
        }
    }

    function updateNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.parentNode.children[7].value;
        var itemQuantity = obj.parentNode.parentNode.children[3].innerHTML;
        itemQuantity = (itemQuantity == "-") ? "NULL" : itemQuantity;
        var itemDate = document.getElementById("session_date").value;

        $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote});
    }

    function updateCateringNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.children[1].value;
        var recipeId = obj.parentNode.children[2].value;
        var orderId = $(".tab_li.selected").attr("id");

        $.post("jq_ajax.php", {updateCateringNotes: "", notes: itemNote, itemId: itemId, recipeId: recipeId, orderId: orderId });
    }

    function updateQuantityCustom(obj) {
        var quantity = obj.value;
        if (quantity < 0) {
            obj.value = "";
        } else {
            quantity = quantity == "" ? 'NULL' : quantity;
            var itemId = obj.parentNode.parentNode.parentNode.parentNode.parentNode.children[7].value;
            var itemDate = document.getElementById("session_date").value;

            $.post("jq_ajax.php", {updateQuantityCustom: "", quantity: quantity, itemId: itemId, itemDate: itemDate});
            updateCost(obj);
        }
        checkRequiredItems();
    }

    function updateOrderNote(obj) {
        var note = obj.value;
        var orderId = $(".tab_li.selected").attr("id");
        $(".selected").find("#order_note").val(note);

        $.post("jq_ajax.php", {updateOrderNote: "", note: note, orderId: orderId});
    }

    function printPdf() {
        createTable(function(table) {
            $("#table_data").val(table.outerHTML);
            switch ($(".print_preview .active").parent().attr("id")) {
                case 'daily_order':
                    document.getElementById("table_name").value = $(".tab_li.selected").children().html();
                    break;
                case 'bulk_order':
                    document.getElementById("table_name").value = "Bulk Order";
                    break;
                case 'catering_order':
                    document.getElementById("table_name").value = $(".tab_li.selected").children().html();
                    break;
            }
            document.getElementById("table_date").value = $("#print_date").children().find("#table_date_span").html();
            $("#test_form").submit();
        });
    }

    function printAll() {
        var date = document.getElementById("session_date").value;
        var expectedSales = $(".print_expected").val();
        var required = $("#toolbar_toggle .switch-input").prop("checked") ? "true" : "false";
        $.post("jq_ajax.php", {printAll: "", date: date, expectedSales: expectedSales, required: required}, function(data, status) {
            $("#table_data").val(data);
            document.getElementById("table_name").value = "Print All";
            document.getElementById("table_date").value = document.getElementById("session_date").value;
            $("#test_form").submit();
        });
    }

    function trackInvoice() {
        var date = document.getElementById("session_date").value;
        switch ($(".print_preview .active").parent().attr("id")) {
            case "daily_order":
                if ($(".selected").parent().find("#inventory_invoice").val() > 0) {
                    $("#invoice_form").submit();
                } else {
                    $.post("jq_ajax.php", {trackInvoice: "", date: date});
                    $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                    $(".tab_ul").find(".selected").parent().find("#inventory_invoice").val(1);
                }
                break;
            case 'bulk_order':
                var dateStart = $("#date_from_val").val();
                var dateEnd = $("#date_to_val").val();
                var qpDate = $("#bulk_qp_date").val();
                if ($("#bulk_invoice").val() > 0) {
                    $("#bulk_invoice_form").submit();
                } else {
                    $.post("jq_ajax.php", {trackBulkInvoice: "", dateCreated: date, dateStart: dateStart, dateEnd: dateEnd, qpDate: qpDate});
                    $("#bulk_invoice").val(1);
                    $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                }
                break;
            case "catering_order":
                var orderId = $(".tab_ul").find(".selected").attr("id");
                if ($(".tab_ul").find(".selected").find("#order_invoice").val() !="") {
                    $("#catering_invoice_form").submit();
                } else {
                    $.post("jq_ajax.php", {updateOrderInvoiceDate: "", orderId: orderId, date: "'"+date+"'"});
                    $(".tab_ul").find(".selected").find("#order_invoice").val(date);
                    $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                }
                break;
        }
    }

    function checkInvoice() {
        switch ($(".print_preview .active").parent().attr("id")) {
            case "daily_order":
                if ($(".selected").parent().find("#inventory_invoice").val() > 0) {
                    $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                } else {
                    $("#div_invoice_send")
                        .removeClass("invoice_view")
                        .addClass("invoice_send")
                        .html("Send Invoice");                }
                break;
            case "bulk_order":
                if ($("#bulk_invoice").val() > 0) {
                    $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                } else {
                    $("#div_invoice_send")
                        .removeClass("invoice_view")
                        .addClass("invoice_send")
                        .html("Send Invoice");
                }
                break;
            case "catering_order":
                if ($(".selected").find("#order_invoice").val() != "") {
                   $("#div_invoice_send")
                        .removeClass("invoice_send")
                        .addClass("invoice_view")
                        .html("View Invoice");
                } else {
                    $("#div_invoice_send")
                        .removeClass("invoice_view")
                        .addClass("invoice_send")
                        .html("Send Invoice");
                }
                break;
        }
    }

    function sendPrint() {
        createTable(function(table) {
            document.getElementById("new_print_data").value = table.outerHTML;
             switch ($(".print_preview .active").parent().attr("id")) {
                case 'daily_order':
                    document.getElementById("print_table_name").value = $(".tab_li.selected").children().html();
                    break;
                case 'bulk_order':
                    document.getElementById("print_table_name").value = "Bulk Order";
                    break;
                case 'catering_order':
                    document.getElementById("print_table_name").value = $(".tab_li.selected").children().html();
                    break;
            }
            document.getElementById("print_table_date").value = $("#print_date").children().find("#table_date_span").html();
            $("#share_popup").css("display", "block");
            $("#print_form").submit();
        });
    }

    function createTable(callBack) {
        var table = document.createElement("table");
        var row_count = 0;
        table.setAttribute("class", "table_view");
        switch ($(".side_nav li .active").parent().attr("id")) {
            case 'daily_order':
                table.innerHTML += "<tr class='row'><th colspan='7' class='table_title'>Daily Order</th></tr>";
                table.innerHTML += "<tr class='row'><th colspan='7' class='heading'> " +
                                    $(".tab_li.selected").children().html(); + "</th></tr>";break;
            case 'bulk_order':
                table.innerHTML += "<tr class='row'><th colspan='7' class='table_title'>Bulk Order</th></tr>";
                break;
            // case 'catering_order':
            //     table.innerHTML += "<tr class='row'><th colspan='7' class='table_title'>Catering Order</th></tr>";
            //     table.innerHTML += "<tr class='row'><th colspan='7' class='heading'> " +
            //                         $(".tab_li.selected").children().html(); + "</th></tr>";
            //     break;
        }
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                var row = document.createElement("TR");
                var cell = "";
                $(this).children().each(function() {
                    if ($(this).hasClass("row_icon")) {
                        var td = document.createElement("TD");
                        cell += td.outerHTML;
                        return true;
                    }
                    if ($(this).children(".div_required").length > 0) {
                        var td = document.createElement("TD");
                        switch ($(".side_nav li .active").parent().attr("id")) {
                            case 'bulk_order':
                                if ($(this).find(".span_qc").val() != "") {
                                    td.innerHTML = $(this).find(".span_qc").val();
                                } else {
                                    td.innerHTML = "-";
                                }
                                break;
                            default:
                                if ($(this).find(".span_qc").val() != "") {
                                    td.innerHTML = $(this).find(".span_qc").val();
                                } else {
                                    td.innerHTML = $(this).find(".span_qr").html();
                                }
                                break;
                        }
                        cell += td.outerHTML;
                    } else if ($(this).children("textarea").length > 0) {
                        var td = document.createElement("TD");
                        td.innerHTML = $(this).children().val();
                        cell += td.outerHTML;
                    } else if ($(this).attr("id") == "hidden_id") {

                    } else {
                        cell += this.outerHTML;
                    }
                });
                row.innerHTML = cell;
                table.innerHTML += row.outerHTML;
                if ($(".side_nav li .active").parent().attr("id") != "catering_order") {
                    switch ($(".side_nav li .active").parent().attr("id")) {
                        case 'daily_order':
                            var expectedSales = $(".print_expected").val() == "" ? "   -" : "    $ " + $(".print_expected").val();
                            break;
                        case 'bulk_order':
                            var expectedSales = $("#total_expected").html() == "" ? "   -" : "    $ " + $("#total_expected").html();
                            break;
                    }
                    row_count == 0 ? table.innerHTML += "<tr class='row'><th colspan='7' class='expected_heading'><span class='print_table_date'>Expected Sales   </span>" +
                                                        "<span>  "+expectedSales+"</span></th></tr>" : "";
                    row_count++;
                }
            }
        });
        var totalCost = "";
        $(".cost").each(function() {
            var value = $(this).html() != "-" ? $(this).html() : "";
            totalCost = +totalCost + +value.replace('$ ', "");
        });
        totalCost != "" ? totalCost = "$" + totalCost  : totalCost = "-";
        table.innerHTML += "<tr><td class='table_heading' colspan='4'><h4>Total Cost</h4></td>"+
                           "<td class='table_heading' colspan='3'><h4>"+totalCost+"</h4></td></tr>";
        if ($(".side_nav li .active").parent().attr("id") == "catering_order") {
            var note = $(".selected").find("#order_note").val() == "" ? "No Special Instructions Added" : $(".selected").find("#order_note").val();
            table.innerHTML += '<tr id="category"><td colspan="7" class="table_title">Special Instructions</td></tr>';
            table.innerHTML +=  '<tr id="column_data" class="row" colspan="5"><td class="order_note" colspan="7">'+note+'</td>'
        }
        callBack(table);
        console.log(table);
    }

    function checkRequired() {
        if ($("#toolbar_toggle .switch-input").prop("checked")) {
            $(".print_tbody").each(function() {
                var total = $(this).find(".quantity_required").length;
                var remove = 0;
            switch ($(".print_preview .active").parent().attr("id")) {
                case 'bulk_order':
                    $(this).find(".quantity_required").each(function() {
                        if ((($(this).find(".span_qc").val() <= 0 || $(this).find(".span_qc").val() == "") &&
                             $(this).nextAll("#td_notes").children("textarea").val() == "")) {
                            $(this).parent().hide();
                            remove++;
                        }
                    });
                    break;
                default:
                    $(this).find(".quantity_required").each(function() {
                        if (($(this).find(".span_qc").val() <= 0 && $(this).find(".span_qc").val() != "") && $(this).nextAll("#td_notes").children("textarea").val() == "") {
                            $(this).parent().hide();
                            remove++;
                        } else if (($(this).find(".span_qc").val() == "" && ($(this).find(".span_qr").html() <= 0 || $(this).find(".span_qr").html() == "-"))  && $(this).nextAll("#td_notes").children("textarea").val() == "") {
                            $(this).parent().hide();
                            remove++;
                        }
                    });
                    break;
            }

            if (total - remove == 0) {
                $(this).hide();
                $(this).children().hide();
            }
            });
        } else {
            $(".print_tbody").each(function() {
                $(this).show();
                $(this).find("tr").show();
            });
        }
    }

    function checkRequiredItems() {
        var count = 0;
        $(".row_icon").each(function() {
            if ($(this).attr("data-required") == "true") {
                if (($(this).parent().find(".span_qr").html() < 1 || $(this).parent().find(".span_qr").html() == "-") &&
                     $(this).parent().find(".span_qc").val() == "") {
                    count++;
                }
            }
        });
        $(".status_view").find("#item_num").html(count);
    }

    function updateCost(obj) {
        var quantity = obj.value;
        var itemId = $(obj).parents("tr").find("#hidden_id").val();
        if (quantity > 0) {
            var price = $(obj).parents("tr").find("#item_price").val();
            if (price > 0 ) {
                var cost = quantity * price;
                $.post("jq_ajax.php", {updateRequiredCost: "", cost: cost, itemId: itemId});
                $(obj).parents("tr").find(".cost").html("$ "+cost);
            } else {
                $.post("jq_ajax.php", {updateRequiredCost: "", cost: 'NULL', itemId: itemId});
                $(obj).parents("tr").find(".cost").html("-");
            }
        } else {
            $.post("jq_ajax.php", {updateRequiredCost: "", cost: 'NULL', itemId: itemId});
            $(obj).parents("tr").find(".cost").html("-");
        }
    }

    function updateBulkCost(obj) {
        var itemDate = $(obj).parents(".div_cell").find("#date").val();
        var itemId = $(obj).parents(".div_cell").find("#item_id").val();
        if (obj.value > 0) {
            var quantity = obj.value;
        } else {
            var quantity = $(obj).parents(".div_cell").find(".span_required").html();
        }
        if (quantity > 0) {
            var price = $(obj).parents(".div_cell").find("#item_price").val();
            if (price > 0 ) {
                var cost = quantity * price;
                $.post("jq_ajax.php", {updateBulkRequiredCost: "", cost: cost, itemId: itemId, itemDate: itemDate});
                $(obj).parents(".div_cell").find(".cost").html(cost);
            } else {
                $.post("jq_ajax.php", {updateBulkRequiredCost: "", cost: 'NULL', itemId: itemId, itemDate: itemDate});
                $(obj).parents(".div_cell").find(".cost").html("0");
            }
        } else {
            $.post("jq_ajax.php", {updateBulkRequiredCost: "", cost: 'NULL', itemId: itemId, itemDate: itemDate});
            $(obj).parents(".div_cell").find(".cost").html("0");
        }
        showTotalBulkCost();
    }

    (function checkExpectedSales() {
        if($(".print_expected").val() == "") {
            $(".daily_expected_sales").css("border", "1px solid red");
            var div = document.createElement("div");
            div.setAttribute("id", "warning");
            div.innerHTML = "enter expected sales";
            $(".daily_expected_sales").append(div);
        }
    })();

    function calculateCustomQp(qpDate) {
        var expectedSales = $(".print_expected").val();
        expectedSales = expectedSales == "" ? 'NULL' : expectedSales;
        $.post("jq_ajax.php", {calcCustomQuantityPresent: "", expectedSales: expectedSales, qpDate: qpDate}, function(status){
            if (status) {
                getTab($(".div_child .tab_li span:first")[0]);
            }
        });
    }

    function calculateBulkCustomQp(qpDate) {
        var dateEnd = $("#date_to_val").val();
        $.post("jq_ajax.php", {calcBulkCustomQuantityPresent: "", dateEnd: dateEnd, qpDate: qpDate}, function(status){
            if (status) {
                getBulkPreview();
            }
        });
    }

    function saveQpDates(qpDate) {
        var dateCreated = $("#session_date").val();

        $.post("jq_ajax.php", {saveQpDates: "", dateCreated: dateCreated, qpDate: qpDate});
    }

    function saveBulkQpDates(qpDate) {
        var dateCreated = $("#session_date").val();

        $.post("jq_ajax.php", {saveBulkQpDates: "", dateCreated: dateCreated, qpDate: qpDate});
    }

    var monthArray = ["JAN","FEB","MAR","APR","MAY","JUN","JUL","AUG","SEP","OCT","NOV","DEC"];

    function createQpDatePicker(obj) {
        var defaultDate = obj.find("input").val();
        $(".ui-datepicker").css("display", "none");
        obj.find(".ui-datepicker").css("display", "block");
        obj.find(".div_cal").datepicker({
                dateFormat: "yy-mm-dd",
                defaultDate: defaultDate,
                dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
                prevText: "previous",
                onSelect: function(dateText) {
                    $(".ui-datepicker").css("display", "none");
                    var dateObj = new Date(dateText.replace(/-/g, '\/'));
                    var dateFormat = dateObj.getDate()+" "+monthArray[dateObj.getMonth()]+" "+dateObj.getFullYear();
                    obj.find("#heading").next().html(dateFormat);
                    obj.find("input").val(dateText);
                    calculateCustomQp(dateText);
                    saveQpDates(dateText);
                }
        });
    }

    function createBulkQpDatePicker(obj) {
        var defaultDate = obj.find("input").val();
        $(".ui-datepicker").css("display", "none");
        obj.find(".ui-datepicker").css("display", "block");
        obj.find(".div_cal").datepicker({
                dateFormat: "yy-mm-dd",
                defaultDate: defaultDate,
                dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
                prevText: "previous",
                onSelect: function(dateText) {
                    $(".ui-datepicker").css("display", "none");
                    var dateObj = new Date(dateText.replace(/-/g, '\/'));
                    var dateFormat = dateObj.getDate()+" "+monthArray[dateObj.getMonth()]+" "+dateObj.getFullYear();
                    obj.find("#heading").next().html(dateFormat);
                    obj.find("input").val(dateText);
                    checkBulkOrderValues();
                    saveBulkQpDates(dateText);
                }
        });
    }

    function createDatePicker(obj) {
        var defaultDate = obj.find("input").val();
        $(".ui-datepicker").css("display", "none");
        obj.find(".ui-datepicker").css("display", "block");
        obj.find(".div_cal").datepicker({
                dateFormat: "yy-mm-dd",
                defaultDate: defaultDate,
                dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
                prevText: "previous",
                onSelect: function(dateText) {
                    $(".ui-datepicker").css("display", "none");
                    var dateObj = new Date(dateText.replace(/-/g, '\/'));
                    var dateFormat = dateObj.getDate()+" "+monthArray[dateObj.getMonth()]+" "+dateObj.getFullYear();
                    obj.find("#heading").next().html(dateFormat);
                    obj.find("input").val(dateText);
                    checkBulkOrderValues();
                    saveBulkDates();
                }
        });
    }

    function createBulkSales() {
        $("#div_total_expected").html("");
        var dateStart = $("#bulk_qp_date").val();
        var dateEnd = $("#date_to_val").val();
        if (dateStart != "" && dateEnd != "") {
            $.post("jq_ajax.php", {getBulkSales: "", dateStart: dateStart, dateEnd: dateEnd}, function(data) {
                $("#div_total_expected").html(data);
                showTotalBulkSales();
            });
        }
    }

    function updateBulkExpSales(obj) {
        var expSales = obj.value;
        var date = $(obj).next().val();
        expSales = expSales == "" ? 'NULL' : expSales;
        $.post("jq_ajax.php", {updateBulkExpSales: "", expSales: expSales, date: date});
        $.post("jq_ajax.php", {calcBulkQuantityRequired: "", expectedSales: expSales, date: date});
        showTotalBulkSales();
    }

    function showTotalBulkSales() {
        var totalSales = 0;
        $("#div_total_expected .row_amount").each(function() {
            if ($(this).val() != "") {
                totalSales += parseFloat($(this).val());
            }
        });
        var totalExpected = totalSales == 0 ? "-" : totalSales;
        var display =  totalExpected == "-" ?  "none" : "inline-block";
        $("#bulk_expected .right #icon").css("display", display);
        $("#div_total_expected").next().find("#total_sales").html(totalExpected);
        $("#bulk_expected").find("#total_expected").html(totalExpected);
        checkTotalExpected();
    }


    function updateBulkNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.parentNode.children[7].value;
        var itemDate = $("#date_from_val").val();

        $.post("jq_ajax.php", {updateBulkNotes: "", itemId: itemId, itemDate: itemDate, itemNote: itemNote});
    }

    function getBulkPreview() {
        var dateFrom = $("#bulk_qp_date").val();
        var dateTo = $("#date_to_val").val();
        var qpDate = $("#bulk_qp_date").val();
        var totalExpected = $("#total_expected").html();
        if (dateFrom != "" && dateTo != "" && totalExpected != "-") {
            $.post("jq_ajax.php", {getBulkPrintPreview: "", dateStart: dateFrom, dateEnd: dateTo, qpDate: qpDate}, function(data) {
                $(".print_tbody").remove();
                $("#print").append(data);
                $("#table_date_span").html($("#date_from").html() + " - " +  $("#date_to").html());
                $("#table_date_span").css("color", "initial");
                $(".print_table_date").css("display", "block");
                $(".div_required").each(function() {
                    if ($(this).find(".span_qc").val() != "") {
                        $(this).find(".tab").trigger("click");
                    }
                });
                getBulkInvoice();
                checkRequired();
            });
        }
    }

    function getBulkInvoice() {
        var dateStart = $("#date_from_val").val();
        var dateEnd = $("#date_to_val").val();
        $.post("jq_ajax.php", {getBulkTrackedInvoice: "", dateStart: dateStart, dateEnd: dateEnd}, function(data) {
            $("#bulk_invoice").val(data);
            checkInvoice();
        });
    }

    function getBulkCustom(obj) {
        var itemId = $(obj).parents("tr").find("#item_id").val();
        var dateStart = $("#bulk_qp_date").val();
        var dateEnd = $("#date_to_val").val();
        $.post("jq_ajax.php", {getBulkCustom: "", itemId: itemId, dateStart: dateStart, dateEnd: dateEnd}, function(data) {
            $("#div_total_custom").html(data);
            showTotalBulkCustom();
            showTotalBulkCost();
        });
    }

    function updateBulkQuantityCustom(obj) {
        var date = $(obj).parents(".div_cell").find("#date").val();
        var itemId = $(obj).parents(".div_cell").find("#item_id").val();
        var value = obj.value;
        value = value != "" ? value : 'NULL';
        $.post("jq_ajax.php", {updateBulkQuantityCustom: "", value: value, itemId: itemId, date: date});
        showTotalBulkCustom();
        updateBulkCost(obj);
    }

      function showTotalBulkCustom() {
        var row = $(".table_view")
                                .children().eq($("#bulk_custom_popup").find("#tbody_index").val())
                                .children().eq($("#bulk_custom_popup").find("#row_index").val());
        var totalSales = 0;
        $("#div_total_custom .row_data").each(function() {
            var quantityRequired = $(this).find(".span_required").html();
            var quantityCustom = $(this).find(".row_amount").val();
            quantityRequired = quantityRequired != "" ? quantityRequired : 0;
            quantityCustom = quantityCustom != "" ? quantityCustom : 0;
            var quantity = quantityCustom > 0 ? quantityCustom : quantityRequired;
            totalSales += parseFloat(quantity);
        });
        totalSales = totalSales == 0 ? "-" : totalSales.toFixed(2);
        $("#div_total_custom").next().find("#total_sales").html(totalSales);
        row.find(".bulk_custom").val(totalSales);

    }

    function showTotalBulkCost() {
        var row = $(".table_view")
                                .children().eq($("#bulk_custom_popup").find("#tbody_index").val())
                                .children().eq($("#bulk_custom_popup").find("#row_index").val());
        var totalCost = 0;
        $("#div_total_custom .cost").each(function() {
            var cost = $(this).html();
            totalCost += parseFloat(cost);
        });
        totalCost = totalCost == 0 ? "-" : "$ "+totalCost;
        $("#div_total_custom").next().find("#total_cost").html(totalCost);
        row.find(".cost").html(totalCost);
    }

    function saveBulkDates() {
        var dateStart = $("#date_from_val").val();
        var dateEnd = $("#date_to_val").val();
        var dateCreated = $("#session_date").val();
        var qpDate = $("#bulk_qp_date").val();

        $.post("jq_ajax.php", {saveBulkDates: "", dateCreated: dateCreated, dateStart: dateStart, dateEnd: dateEnd, qpDate: qpDate});
    }

    function checkBulkOrderValues() {
        if ($("#bulk_expected #date_from").html() == "Enter Date") {
            checkDateFrom();
        } else if($("#bulk_expected #date_to").html() == "Enter Date") {
            checkDateTo();
        } else {
            createBulkSales();
        }
    }

    function checkDateFrom() {
        $("#bulk_expected .left").css({"border": "1px solid red"});
        $("#bulk_expected .center").addClass("blur");
        $("#bulk_expected .right").addClass("blur");
        $("#table_date_span").html("***Enter Date From***");
        $("#table_date_span").css("color", "red");
        $(".print_table_date").css("display", "none")
        $("#date_from").trigger("click");
    }

    function checkDateTo() {
        $("#bulk_expected .left").css({"border": "1px solid #cecece", "border-top": "none"});
        $("#bulk_expected #bulk_expected_view").css({"border": "1px solid red"});
        $("#bulk_expected div").removeClass("blur");
        $("#bulk_expected .right").addClass("blur");
        $("#bulk_expected .bulk_qp_date").addClass("blur");
        $("#table_date_span").html("***Enter Date To***");
        $("#table_date_span").css("color", "red");
        $(".print_table_date").css("display", "none");
        $("#date_to").trigger("click");
    }

    function checkTotalExpected() {
        $("#bulk_expected .center").css({"border": "1px solid #cecece", "border-top": "none"});
        $("#bulk_expected div").removeClass("blur");
        var emptyField = 0;
        $("#div_total_expected .row_amount").each(function() {
            if (!$(this).val()) {
                emptyField++;
            }
        });
        if (emptyField > 0) {
            $("#bulk_expected .right").css("border", "1px solid red");
            $(".print_tbody").remove();
            $("#table_date_span").html("***Enter Expected Sales***");
            $("#table_date_span").css("color", "red");
            $(".print_table_date").css("display", "none");
            $("#bulk_expected .right").trigger("click");
        } else{
            $("#bulk_expected .right").css({"border": "1px solid #cecece", "border-top": "none"});
            calculateBulkCustomQp($("#bulk_qp_date").val());
        }
    }

    $(document).ready(function() {
        $(".side_nav li a:first").each(function() {
           $(this).addClass("active");
        });

        $(".div_child .tab_li span:first").each(function() {
           getTab($(this)[0]);
           $(this).parent().addClass("selected");
           checkInvoice();
        });

        $('.side_nav li a').click(function() {
            $('.side_nav li a').removeClass("active");
            $(this).addClass('active');
        });

        $("#catering_order").click(function() {
            $(".status_view").css("z-index", "-1");
            $(".status_view").removeClass("visible");
            $("#inventory_tabs").css("display", "none");
            $(".div_expected").css("display", "none");
            $("#print_all").parent().css("display", "flex");
            $("#div_table").css("margin-top", "53px");

            if ($("#catering_order #right").html() > 0) {
                $("#catering_tabs").css("display", "block");
                $("#table_date_heading").html("delivery date");
                $("#catering_tabs .tab_li:first").each(function() {
                    getOrderTab($(this)[0]);
                    $(".div_child .tab_li").removeClass("selected");
                    $(this).addClass("selected");
                    $("#order_note_div").css("display", "block");
                    $("#note_text").html($(".selected").find("#order_note").val());
                    $("#note_text").val($(".selected").find("#order_note").val());
                    checkInvoice();
                });
            } else {
                $("#table_date_heading").html("No Upcoming Catering Orders.");
                $("#table_date_span").html("");
                $(".print_table_date").css("display", "none");
                $(".print_tbody").remove();
                $("#invoice_checkbox").prop("checked", false);
            }
        });

        $("#daily_order").click(function() {
            $("#table_date_heading").html("");
            $(".print_tbody").remove();
            $("#print_all").parent().css("display", "flex");
            $("#div_table").css("margin-top", "35px");
            $("#catering_tabs").css("display", "none");
            $("#inventory_tabs").css("display", "block");
            $("#order_note_div").css("display", "none");
            $("#bulk_expected").css("display", "none");
            $("#daily_expected").css("display", "flex");
            $("#inventory_tabs .tab_li span:first").each(function() {
               getTab($(this)[0]);
               $(".div_child .tab_li").removeClass("selected");
               $(this).parent().addClass("selected");
               checkInvoice();
            });
        });

        $("#bulk_order").click(function() {
            $(".status_view").css("z-index", "-1");
            $(".status_view").removeClass("visible");
            $("#table_date_heading").html("");
            $(".print_tbody").remove();
            $("#print_all").parent().css("display", "none");
            $("#div_table").css("margin-top", "35px");
            $("#catering_tabs").css("display", "none");
            $("#inventory_tabs").css("display", "none");
            $("#order_note_div").css("display", "none");
            $("#daily_expected").css("display", "none");
            $("#bulk_expected").css("display", "flex");
            checkBulkOrderValues();
        });

        $(".div_child .tab_li").click(function() {
            $(".div_child .tab_li").removeClass("selected");
            $(this).addClass("selected");
            $("#note_text").html($(".selected").find("#order_note").val());
            $("#note_text").val($(".selected").find("#order_note").val());
            checkInvoice();
        });

        $(".popup_close").click(function() {
            $(this).parents(".div_popup_back").fadeOut(190, "linear");
            $(".main_iframe").removeClass("blur");
        });

        $(document).on("click", ".tab", function() {
            $(this).parent().find(".selected").removeClass("selected");
            $(this).addClass("selected");
            if ($(this).attr("id") == "calculated") {
                $(this).parents("td").find(".span_qc").css("display", "none");
                $(this).parents("td").find(".span_qr").css("display", "block");
                $(this).parents("td").find("#heading").html("calculated value");
                updateCost($(this).parents("td").find(".span_qr")[0]);
            } else {
                $(this).parents("td").find(".span_qr").css("display", "none");
                $(this).parents("td").find(".span_qc").css("display", "block");
                $(this).parents("td").find("#heading").html("custom value");
                updateCost($(this).parents("td").find(".span_qc")[0]);
            }
        });

        $(document).on("click", ".edit_span", function() {
            $(this).parents("td").find(".span_qr").css("display", "none");
            $(this).parents("td").find(".span_qc").css("display", "block");
        });

        $(".status_view").click(function(event) {
            event.stopPropagation();
            var allViewed = true;
            $(".icon.fa-star").each(function() {
                if (($(this).parents("tr").find(".span_qr").html() < 1 || $(this).parents("tr").find(".span_qr").html() == "-") &&
                    $(this).parents("tr").find(".span_qc").val() == "") {
                    if (!$(this).hasClass("viewed")) {
                        $(this).addClass("viewed");
                        $("#div_print_table").animate({
                           scrollTop: $(this).parents("tr").position().top
                        }, 200);
                        $(this).parents("tr").find(".tab").trigger("click");
                        allViewed = false;
                        return false;
                    }
                }
            });
            if (allViewed == true) {
                $(".icon.fa-star").removeClass("viewed");
                $(".icon.fa-star").each(function() {
                    if (($(this).parents("tr").find(".span_qr").html() < 1 || $(this).parents("tr").find(".span_qr").html() == "-") &&
                        $(this).parents("tr").find(".span_qc").val() == "") {
                        if (!$(this).hasClass("viewed")) {
                            $(this).addClass("viewed");
                            $("#div_print_table").animate({
                               scrollTop: $(this).parents("tr").position().top
                            }, 200);
                            $(this).parents("tr").find(".tab").trigger("click");
                            allViewed = false;
                            return false;
                        }
                    }
                });
            }
        });

        $(".daily_qp_date").click(function() {
            createQpDatePicker($(this));
        });

        $(".bulk_qp_date").click(function() {
            createBulkQpDatePicker($(this));
             $(this).find(".div_cal").datepicker("option", "maxDate", $("#date_from_val").val());
        });

        $("#bulk_expected .right").click(function() {
            $("#expected_popup").css("display", "block");
        });

        $(document).on("click", ".bulk_custom", function() {
            $("#div_total_custom").html("");
            $("#bulk_custom_popup").find("#tbody_index").val($(this).parents("tbody").index());
            $("#bulk_custom_popup").find("#row_index").val($(this).parents("tr").index());
            getBulkCustom(this);
            $("#bulk_custom_popup").css("display", "block");
        });

        $("#date_from").click(function() {
            createDatePicker($(this).parent());
            $(this).parent().find(".div_cal").datepicker("option", "maxDate", $("#date_to_val").val());
        });

        $("#date_to").click(function() {
            createDatePicker($(this).parent());
            $(this).parent().find(".div_cal").datepicker("option", "minDate", $("#date_from_val").val());
        });

        $("#bulk_expected_done").click(function() {
            var emptyField = 0;
            $("#div_total_expected .row_amount").each(function() {
                if (!$(this).val()) {
                    emptyField++;
                    $(this).css("border", "1px solid red");
                }
            });
            if (emptyField == 0) {
                getBulkPreview();
                $(this).parents(".div_popup_back").fadeOut(190, "linear");
            }
        });

         $(document).on("click", "#div_total_expected .row_amount", function()  {
            $(this).css({"border": "none", "border-bottom": "1px solid #cecece"});
         });

        $("#custom_quantity_done").click(function() {
            $(this).parents(".div_popup_back").fadeOut(190, "linear");
        });

         $(document).click(function(event) {
            if(!$(event.target).closest('input').length && !$(event.target).is("a, span")) {
                if($('.ui-datepicker').is(":visible")) {
                    $('.div_cal .ui-datepicker').css("display", "none");
                }
            }
        });
    });
</script>
