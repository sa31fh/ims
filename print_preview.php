<?php
session_start();
require_once "database/timeslot_table.php";
require_once "database/sales_table.php";
require_once "database/invoice_table.php";
require_once "database/sales_table.php";
require_once "database/catering_order_table.php";
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
            <ul class="side_nav">
                <li id="order_li"><a >Inventory</a></li>
                <li id="order_li">
                    <a id="catering_option">
                        <span id="left">Catering Orders</span>
                        <span id="right"><?php echo CateringOrderTable::get_order_count($_SESSION["date"], $future_date); ?></span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main_top_side">
            <div class="toolbar_print">
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
                <div class="toolbar_div" id="invoice_div">
                    <?php $result = InvoiceTable::get_tracked($_SESSION["date"])->fetch_assoc();?>
                    <span id="track_invoice" class="fa-file-text-o">Invoice</span>
                    <label class="switch" id="toolbar_toggle">
                        <input id="invoice_checkbox" class="switch-input" type="checkbox" <?php echo count($result) < 1 ? "" : "" ?> onclick=trackInvoice(this) />
                        <span class="switch-label" data-on="Tracking" data-off="off"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
            </div>

        <?php if ($_SESSION["userrole"] == "admin"): ?>
            <div class="div_expected">
                <div id="left">
                    <span id="heading">Todays sales</span>
                    <span id="amount">
                    <?php $todays_sales = SalesTable::get_actual_sale($_SESSION["date"]);
                    echo is_null($todays_sales) ? "-" :  "$ ".$todays_sales;?>
                    </span>
                </div>
                <div id="center">
                    <span id="heading">Expected Sales</span>
                    <form action="print_preview.php" method="post" id="expected_form">
                        <span id="icon">$</span>
                        <input class="print_expected" type="number" name="expected_sales" value="<?php echo SalesTable::get_expected_sale($_SESSION['date']) ?>" onchange=updateExpectedSales(this)>
                    </form>
                </div>
                <div id="right">
                    <?php $date =  date_sub(date_create($_SESSION["date"]), date_interval_create_from_date_string("6 days"));?>
                    <span id="heading"><?php echo "last ".date_format($date, "l")."s sales" ?></span>
                    <span id="amount">
                    <?php $last_week = date_format($date, "Y-m-d");
                    $sales = SalesTable::get_actual_sale($last_week);
                    echo is_null($sales) ? "-" : "$ ".$sales;?>
                    </span>
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
                                    </li>
                                </div>
                            <?php endwhile ?>
                        </ul>
                    </div>
                </div>
                <div id="div_print_table">
                    <table class="table_view" id="print">
                        <tr id="print_date" class="row">
                            <th colspan="6">
                                <div id="table_date_heading"></div>
                                <span id="table_date_span"><?php echo date_format((date_add(date_create($_SESSION["date"]), date_interval_create_from_date_string("1 day"))), 'D, jS M Y'); ?></span>
                                <div class="print_table_date"><?php echo "created on ".date('jS M Y', strtotime($_SESSION["date"])); ?></div>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="div_popup_back">
        <div class="div_popup popup_share">
            <div class="popup_titlebar">
                <span>New Message</span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <iframe id="popup_frame" name="popup_frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"] ?>">
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

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function getTab(tabName) {
        var timeSlotName = tabName.innerHTML;
        var date = document.getElementById("session_date").value;
        if (timeSlotName == "Full Day") {
            $.post("jq_ajax.php", {getPrintPreview: "", date: date}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                $("#table_date_span").html($("#formatted_date").val());
                $(".print_table_date").css("display", "block");
            });
        } else {
            $.post("jq_ajax.php", {getPrintPreviewTimeslots: "", date: date, timeSlotName: timeSlotName}, function(data, status) {
                $(".print_tbody").remove();
                $("#print").append(data);
                $(".print_table_date").css("display", "block");
                $("#table_date_span").html($("#formatted_date").val());
            });
        }
        checkRequired();
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
        } else {
            obj.parentNode.submit();
        }
    }

    function updateNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.children[1].value;
        var itemQuantity = obj.parentNode.parentNode.children[2].innerHTML;
        itemQuantity = (itemQuantity == "-") ? "NULL" : itemQuantity;
        var itemDate = document.getElementById("session_date").value;

        $.post("jq_ajax.php", {itemId: itemId, itemDate: itemDate, itemQuantity: itemQuantity, itemNote: itemNote});
    }

    function updateCateringNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.children[1].value;
        var orderId = $(".tab_li.selected").attr("id");

        $.post("jq_ajax.php", {updateCateringNotes: "", notes: itemNote, itemId: itemId, orderId: orderId });
    }

    function printPdf() {
        createTable(function(table) {
            $("#table_data").val(table.outerHTML);
            document.getElementById("table_name").value = $(".tab_li.selected").children().html();
            document.getElementById("table_date").value = $("#print_date").children().find("#table_date_span").html();
            $("#test_form").submit();
        });
    }

    function trackInvoice(obj) {
        var date = document.getElementById("session_date").value;
        if ($(".active").html() == "Inventory") {
            if ($(obj).prop("checked")) {
                $.post("jq_ajax.php", {trackInvoice: "", date: date});
                $(".selected").parent().find("#inventory_invoice").val(1);
            } else {
                $.post("jq_ajax.php", {removeInvoice: "", date: date});
                $(".selected").parent().find("#inventory_invoice").val(0);
            }
        } else {
            var orderId = $(".selected").attr("id");
            if ($(obj).prop("checked")) {
                $.post("jq_ajax.php", {updateOrderInvoiceDate: "", orderId: orderId, date: "'"+date+"'"});
                $(".selected").find("#order_invoice").val(date);
            } else {
                $.post("jq_ajax.php", {updateOrderInvoiceDate: "", orderId: orderId, date: "NULL"});
                $(".selected").find("#order_invoice").val("");
            }
        }

    }

    function checkInvoice() {
        if ($(".active").html() == "Inventory") {
            if ($(".selected").parent().find("#inventory_invoice").val() > 0) {
                $("#invoice_checkbox").prop("checked", true);
            } else {
                $("#invoice_checkbox").prop("checked", false);
            }
        } else {
            if ($(".selected").find("#order_invoice").val() != "") {
                $("#invoice_checkbox").prop("checked", true);
            } else {
                $("#invoice_checkbox").prop("checked", false);
            }
        }
    }

    function sendPrint() {
        createTable(function(table) {
            document.getElementById("new_print_data").value = table.outerHTML;
            document.getElementById("print_table_name").value = $(".tab_li.selected").children().html();
            document.getElementById("print_table_date").value = $("#print_date").children().find("#table_date_span").html();
            $(".div_popup_back").css("display", "block");
            $("#print_form").submit();
        });
    }

    function createTable(callBack) {
        var table = document.createElement("table");
        var row_count = 0;
        table.setAttribute("class", "table_view");
        if ($(".side_nav li .active").html() != "Inventory") {
           table.innerHTML += "<tr class='row'><th colspan='6'>Catering Order</th></tr>";
        }
        table.innerHTML += "<tr class='row'><th colspan='6' class='heading'> " +
                            $(".tab_li.selected").children().html(); + "</th></tr>";
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                var row = document.createElement("TR");
                var cell = "";
                $(this).children().each(function() {
                    if ($(this).children("textarea").length > 0) {
                        var td = document.createElement("TD");
                        td.innerHTML = $(this).children().val();
                        cell += td.outerHTML;
                    } else {
                        cell += this.outerHTML;
                    }
                });
                row.innerHTML = cell;
                table.innerHTML += row.outerHTML;
                if ($(".side_nav li .active").html() == "Inventory") {
                    row_count == 0 ? table.innerHTML += "<tr class='row'><th colspan='6' class='expected_heading'><span class='print_table_date'>Expected Sales</span>" +
                                                        "<span> $"+ $(".print_expected").val() +"</span></th></tr>" : "";
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
        table.innerHTML += "<tr><td class='table_heading' colspan='3'><h4>Total Cost</h4></td>"+
                           "<td class='table_heading' colspan='3'><h4>"+totalCost+"</h4></td></tr>";
        callBack(table);
    }

    function checkRequired() {
        if ($("#toolbar_toggle .switch-input").prop("checked")) {
            $(".print_tbody").each(function() {
                var total = $(this).find(".quantity_required").length;
                var remove = 0;
                $(this).find(".quantity_required").each(function() {
                  if ((this.innerHTML <=0 || this.innerHTML == "-") && $(this).nextAll("#td_notes").children("textarea").val() == "") {
                    $(this).parent().hide();
                    remove++;
                  }
                });
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

    (function checkExpectedSales() {
        if($(".print_expected").val() == "") {
            $("#center").css("border", "1px solid red");
            var div = document.createElement("div");
            div.setAttribute("id", "warning");
            div.innerHTML = "enter expected sales";
            var parent = document.getElementById("center");
            parent.appendChild(div);
        }
    })();

    $(document).ready(function() {
        $(".side_nav li:first").each(function() {
           $(this).children().addClass("active");
        });

        $(".div_child .tab_li span:first").each(function() {
           getTab($(this)[0]);
           $(this).parent().addClass("selected");
           checkInvoice();
        });

        $('.side_nav li a').click(function() {
            $('.side_nav li a').removeClass("active");
            $(this).addClass('active');
            if ($(this).html() == "Inventory") {
                $("#div_table").css("margin-top", "35px");
                $("#catering_tabs").css("display", "none");
                $("#inventory_tabs").css("display", "block");
                $(".div_expected").css("display", "flex");
                $("#table_date_heading").html("");
                $("#inventory_tabs .tab_li span:first").each(function() {
                   getTab($(this)[0]);
                   $(".div_child .tab_li").removeClass("selected");
                   $(this).parent().addClass("selected");
                   checkInvoice();
                });
            } else {
                $("#inventory_tabs").css("display", "none");
                $(".div_expected").css("display", "none");
                $("#div_table").css("margin-top", "15px");

                if ($("#catering_option #right").html() > 0) {
                    $("#catering_tabs").css("display", "block");
                    $("#table_date_heading").html("delivery date");
                    $("#catering_tabs .tab_li:first").each(function() {
                       getOrderTab($(this)[0]);
                       $(".div_child .tab_li").removeClass("selected");
                       $(this).addClass("selected");
                       checkInvoice();
                    });
                } else {
                    $("#table_date_heading").html("No Upcoming Catering Orders.");
                    $("#table_date_span").html("");
                    $(".print_table_date").css("display", "none");
                    $(".print_tbody").remove();
                    $("#invoice_checkbox").prop("checked", false);
                }
            }
        });

        $(".div_child .tab_li").click(function() {
            $(".div_child .tab_li").removeClass("selected");
            $(this).addClass("selected");
            checkInvoice();
        });

        $("#popup_close").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
            $(".main_iframe").removeClass("blur");
        });
    });
</script>