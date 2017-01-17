<?php
session_start();
require_once "database/invoice_table.php";
require_once "mpdf/vendor/autoload.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["userrole"] != "admin") {
    header("Location: login.php");
    exit();
}
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
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
$_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden font_open_sans">
    <div class="main overflow_hidden">
        <ul class="sidenav font_roboto" id="sideNav">
            <li id="heading"><h4>Tracked Invoices</h4></li>
            <?php $result = InvoiceTable::get_tracked_invoices();
            while ($row = $result->fetch_assoc()) :
            $date = date_add(date_create($row["date"]), date_interval_create_from_date_string("1 day")); ?>
            <li>
                <a class="invoice_date" onclick="showInvoice(this)">
                    <div id="left">
                        <span><?php echo date_format($date, "jS"); ?></span>
                    </div>
                    <div id="right">
                        <span id="top"><?php echo date_format($date, "F"); ?></span>
                        <span id="bottom"><?php echo date_format($date, "D Y"); ?></span>
                    </div>
                    <input type="hidden" id="selected_date" value="<?php echo date_format($date, "jS F Y") ?>">
                    <input type="hidden" id="created_date" value="<?php echo date_format(date_create($row["date"]), "jS F Y") ?>">
                </a>
                <input type="hidden" value="<?php echo $row["date"] ?>">
            </li>
            <?php endwhile?>
        </ul>

        <div class="main_top_side">
            <div class="toolbar_print"  id="invoice_toolbar">
                <label class="switch">
                    <input class="switch-input" type="checkbox" onclick=checkRequired() />
                    <span class="switch-label" data-on="Required" data-off="All"></span>
                    <span class="switch-handle"></span>
                </label>
                <div class="divider"></div>
                <div class="toolbar_div">
                    <a id="print_share" class="option" onclick=sendPrint()>Share</a>
                </div>
                <div class="divider"></div>
                <div class="toolbar_div">
                    <a id="print_pdf" class="option" onclick=printPdf()>PDF</a>
                </div>
                <div class="toolbar_div float_right" id="totalcost_div">
                    <span id="label">total cost</span>
                    <span id="cost_span"></span>
                </div>
            </div>

            <div class="div_invoice_table">
                <table class="table_view" id="invoice_table">
                    <tr id="print_date" class="row">
                        <th colspan="6">
                            <span></span>
                            <div class="print_table_date"></div>
                        </th>
                    </tr>
                </table>
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

    <form action="compose_messages.php" method="post" id="print_form" target="popup_frame">
        <input type="hidden" id="print_table_date" name="print_table_date">
        <input type="hidden" id="print_table_name" name="print_table_name">
        <input type="hidden" id="new_print_data" name="new_print_data">
    </form>

    <form action="invoice.php" method="post" id="test_form" name="test_form">
        <input type="hidden" id="table_data" name="table_data">
        <input type="hidden" id="table_date" name="table_date">
        <input type="hidden" id="table_name" name="table_name">
    </form>

    <?php $page = "invoice";
    include_once "new_nav.php" ?>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>

    function showInvoice(obj) {
        var date = obj.parentNode.children[1].value;

        $.post("jq_ajax.php", {getTrackedInvoice: "", date: date}, function(data, status) {
            $(".print_tbody").remove();
            $("#invoice_table").append(data);
            checkRequired();
            totalCost();
        });
    }

    function updateQuantity(obj) {
        if (obj.value < 0 ) {
            obj.value = "";
        } else {
        var date = $(".invoice_date.active").next().val();
        var itemId = obj.parentNode.parentNode.children[6].value;
        var quantity = obj.value;
        quantity == "" ? quantity = "NULL" : quantity;

        $.post("jq_ajax.php", {updateQuantityDelivered: "", quantity: quantity, itemId: itemId, date: date});
        quantity != "NULL" ? updateCost(itemId, quantity, obj) : obj.parentNode.parentNode.children[4].innerHTML = "-";
        checkRequired();
        }
    }

    function updateCost(itemId, quantity, obj) {
        $.post("jq_ajax.php", {getItemPrice: "", itemId: itemId}, function(data) {
            var price = data;
            var cost = quantity * price;
            obj.parentNode.parentNode.children[4].innerHTML = "$ " + cost;
            totalCost();
        });
    }

    function updateNotes(obj) {
        var date = $(".invoice_date.active").next().val();
        var itemId = obj.parentNode.parentNode.children[6].value;
        var note = obj.value;

        $.post("jq_ajax.php", {updateInvoiceNotes: "", note: note, itemId: itemId, date: date});
        checkRequired();
    }

    function checkRequired() {
        if ($(".switch-input").prop("checked")) {
            $(".print_tbody").each(function() {
                var total = $(this).find("tr > input").length;
                var remove = 0;
                $(this).find("tr input").each(function() {
                  if ((this.value <=0 || this.value == "") && $(this).parent().nextAll("#td_notes").children("textarea").val() == ""
                       && (($(this).parent().prev().html() == "-") || $(this).parent().prev().html() <= 0)) {
                    $(this).parent().parent().hide();
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

    function sendPrint() {
        createTable(function(table) {
            document.getElementById("new_print_data").value = table.outerHTML;
            document.getElementById("print_table_name").value = "Invoice";
            document.getElementById("print_table_date").value = $("#print_date").children().children().html();
            $(".div_popup_back").css("display", "block");
            $("#print_form").submit();
        });
    }

    function printPdf() {
        createTable(function(table) {
            $("#table_data").val(table.outerHTML);
            document.getElementById("table_name").value = "Invoice";
            document.getElementById("table_date").value = $("#print_date").children().children().html();
            $("#test_form").submit();
        });
    }

    function createTable(callBack) {
        var table = document.createElement("table");
        table.setAttribute("class", "table_view");
        table.innerHTML += "<tr class='row'><th colspan='6' class='heading'>Invoice</th></tr>";
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                var row = document.createElement("TR");
                var cell = "";
                $(this).children().each(function() {
                    if ($(this).children().attr("id") == "quantity_delivered" || $(this).children("textarea").length > 0) {
                        var td = document.createElement("TD");
                        td.innerHTML = $(this).children().val();
                        cell += td.outerHTML;
                    } else {
                        cell += this.outerHTML;
                    }
                });
                row.innerHTML = cell;
                table.innerHTML += row.outerHTML;
            }
        });
        var totalCost = $("#cost_span").html();
        table.innerHTML += "<tr><td class='table_heading' colspan='3'><h4>Total Cost</h4></td>"+
                           "<td class='table_heading' colspan='3'><h4>"+totalCost+"</h4></td></tr>";
        callBack(table);
    }

    function totalCost() {
        var totalCost = "";
        $(".cost").each(function() {
            var value = $(this).html() != "-" ? $(this).html() : "";
            totalCost = +totalCost + +value.replace('$ ', "");
        });
        var costSpan = document.getElementById("cost_span");
        totalCost != "" ? costSpan.innerHTML = "$" + totalCost  : costSpan.innerHTML = "-";
    }

    $(document).ready(function() {

        $("#sideNav li:nth-child(2)").each(function() {
            showInvoice($(this).children()[0]);
            $(this).children().addClass("active");
            $("#print_date span").html($(this).find("#selected_date").val());
            $("#print_date div").html("created on " + $(this).find("#created_date").val());
        });

        $('#sideNav li a').click(function() {
            $('#sideNav li a').removeClass("active");
            $(this).addClass('active');
            $("#print_date span").html($(this).find("#selected_date").val());
            $("#print_date div").html("created on " + $(this).find("#created_date").val());
        });

         $("#popup_close").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
            $(".main_iframe").removeClass("blur");
        });

     });

</script>

