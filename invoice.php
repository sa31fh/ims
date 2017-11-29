<?php
session_start();
require_once "database/invoice_table.php";
require_once "database/catering_order_table.php";
require_once "database/variables_table.php";
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
        <div class="sidenav" id="invoice_sidenav">
            <div id="heading"><h4>Tracked Invoices</h4></div>
            <ul class="side_nav" id="invoice_ul">
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
                        <input type="hidden" id="selected_date" value="<?php echo date_format($date, "D, jS M Y") ?>">
                        <input type="hidden" id="created_date" value="<?php echo date_format(date_create($row["date"]), "jS M Y") ?>">
                    </a>
                    <input type="hidden" value="<?php echo $row["date"] ?>">
                </li>
                <?php endwhile?>
            </ul>
            <ul class="display_none side_nav" id="catering_ul" >
          <?php $result = CateringOrderTable::get_order_invoice();
                $delivery_date = 1;
                while ($row = $result->fetch_assoc()): ?>
                <?php if ($row["date_delivery"] != $delivery_date): ?>
                  <?php $delivery_date = $row["date_delivery"];?>
                        <li class="heading">
                            <span><?php echo date("jS M Y, l", strtotime($row["date_delivery"])); ?></span>
                        </li>
                <?php endif ?>
                    <li id="order_li">
                        <a class="flex_col" onclick="getCateringInvoice(this)">
                            <span id="order_name"><?php echo $row["name"] ?></span>
                            <span id="order_date_created">created on <?php echo date("jS M Y", strtotime($row["date_created"]))?></span>
                        </a>
                        <input id="order_id" type="hidden" value="<?php echo $row["id"] ?>">
                        <input id="order_date" type="hidden" value="<?php echo $row["date_delivery"] ?>">
                        <input type="hidden" id="order_date_format" value="<?php echo date("D, jS M Y", strtotime($row["date_delivery"])); ?>">
                    </li>
                <?php endwhile ?>
            </ul>
            <div class="toolbar_print">
                <div class="toolbar_div option selected" id="invoice_tab">
                   <span class="icon_small fa-file-text"></span>
                   <span class="icon_small_text">Inventory</span>
                </div>
                <div class="toolbar_div option" id="catering_tab">
                    <span class="icon_small fa-cutlery"></span>
                    <span class="icon_small_text">Catering</span>
                </div>
            </div>
        </div>

        <div class="main_top_side">
            <div class="toolbar_print"  id="invoice_toolbar">
                <div class="toolbar_div">
                    <label class="switch">
                        <input class="switch-input" type="checkbox" onclick=checkRequired() />
                        <span class="switch-label" data-on="Required" data-off="All"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
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
                    <span id="cost_span">-</span>
                    <span id="tax_span">w/tax</span>
                    <span id="tax_cost">-</span>
                    <input type="hidden" id="sales_tax" value="<?php echo Variablestable::get_sales_tax(); ?>">
                </div>
            </div>

            <div class="div_invoice_table">
                <table class="table_view" id="invoice_table">
                    <tr id="print_date" class="row">
                        <th colspan="8">
                            <div id="table_date_heading"></div>
                            <span id="table_date_span"></span>
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
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
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

    function getCateringInvoice(obj) {
        var orderId = obj.parentNode.children[1].value;

        $.post("jq_ajax.php", {getCateringOrderInvoice: "", orderId: orderId}, function(data, status) {
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
            var itemName = $(obj).parents("tr").find("#item_name").html();
            var date = $(".invoice_date.active").next().val();
            var itemId = $(obj).parents("tr").find("#item_id").val();
            var quantity = obj.value;
            quantity == "" ? quantity = "NULL" : quantity;

            $.post("jq_ajax.php", {updateQuantityReceived: "", quantity: quantity, itemId: itemId, date: date}, function(data) {
                if (data) {
                        alertify
                        .delay(2000)
                        .success("Changes Saved");
                }
            })
            .fail(function() {
                alertify
                    .maxLogItems(10)
                    .delay(0)
                    .closeLogOnClick(true)
                    .error("Changes for Item '"+itemName+"' did not saved. Click here to try again", function(event) {
                        updateQuantity(obj);
                    });
            });
            updateCost(itemId, quantity, obj);
        }
    }

     function markCustom(obj) {
        var num = parseFloat(obj.value).toFixed(2);
        if ($(obj).val() == "") {
            $(obj).parents("tr").find(".row_mark").removeClass("marked_warning");
            $(obj).parents("tr").find(".row_mark .text").html("not received");
            $(obj).parents("tr").find("#quantity_received").parent().removeClass("field_warning");
            $(obj).prop("readonly", false);
        } else if (num != $(obj).parents("tr").find("#quantity_delivered").html()) {
            $(obj).parents("tr").find(".row_mark").addClass("marked_warning");
            $(obj).parents("tr").find(".row_mark .text").html("received <br> discrepancy");
            $(obj).parents("tr").find("#quantity_received").parent().addClass("field_warning");
            $(obj).prop("readonly", true);
        } else {
            $(obj).parents("tr").find(".row_mark").addClass("marked");
            $(obj).parents("tr").find(".row_mark .text").html("received");
            $(obj).prop("readonly", true);
        }
    }

    function updateCost(itemId, quantity, obj) {
        var date = $(".invoice_date.active").next().val();
        var cost = "";
        if (quantity != "NULL") {
            $.post("jq_ajax.php", {getItemPrice: "", itemId: itemId}, function(data) {
                var price = data;
                cost = quantity * price;
                $(obj).parents("tr").find(".cost").html("$ " + cost);
                totalCost();
                saveCost();
            });
        } else {
            $(obj).parents("tr").find(".cost").html("-");
            cost = "NULL";
            totalCost();
            saveCost();
        }
        function saveCost() {
            if ($(".option.selected").find(".icon_small_text").html() == "Inventory") {
                $.post("jq_ajax.php", {updateCostDelivered: "", cost: cost, itemId: itemId, date: date});
            }
        }
    }

    function updateNotes(obj) {
        var date = $(".invoice_date.active").next().val();
        var itemId = obj.parentNode.parentNode.children[8].value;
        var note = obj.value;

        $.post("jq_ajax.php", {updateInvoiceNotes: "", note: note, itemId: itemId, date: date}, function(data) {
            if (data) {
                alertify
                    .delay(2000)
                    .success("Changes Saved");
            }
        })
         .fail(function() {
            alertify
                .maxLogItems(10)
                .delay(0)
                .closeLogOnClick(true)
                .error("Changes for Item '"+itemName+"' did not saved. Click here to try again", function(event) {
                    updateNotes(obj);
                });
        });
    }

    function updateCateringNotes(obj) {
        var itemNote = obj.value;
        var itemId = obj.parentNode.parentNode.children[6].value;
        var recipeId = obj.parentNode.parentNode.children[7].value;
        var orderId = $(".active").parent().find("#order_id").val();

        $.post("jq_ajax.php", {updateCateringInvoiceNotes: "", notes: itemNote, itemId: itemId, recipeId: recipeId, orderId: orderId });
    }

    function updateCateringQuantity(obj) {
        if (obj.value < 0) {
            obj.value = "";
        } else {
            var quantity = obj.value  == "" ? "NULL" : obj.value;
            var itemId = obj.parentNode.parentNode.children[6].value;
            var recipeId = obj.parentNode.parentNode.children[7].value;
            var orderId = $(".active").parent().find("#order_id").val();

            $.post("jq_ajax.php", {updateCateringInvoiceQuantity: "", quantity: quantity, itemId: itemId, recipeId: recipeId, orderId: orderId });
            updateCost(itemId, quantity, obj);
        }
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
            var name = $(".option.selected").find(".icon_small_text").html() == "Inventory"
                       ? "Inventory Invoice" : "Catering Order Invoice - "+$(".active").find("#order_name").html();
            document.getElementById("new_print_data").value = table.outerHTML;
            document.getElementById("print_table_name").value = name;
            document.getElementById("print_table_date").value = $("#print_date").children().find("#table_date_span").html();
            $(".div_popup_back").css("display", "block");
            $("#print_form").submit();
        });
    }

    function printPdf() {
        createTable(function(table) {
            var name = $(".option.selected").find(".icon_small_text").html() == "Inventory"
                       ? "Inventory Invoice" : "Catering Order Invoice - "+$(".active").find("#order_name").html();
            $("#table_data").val(table.outerHTML);
            document.getElementById("table_name").value = name;
            document.getElementById("table_date").value = $("#print_date").children().find("#table_date_span").html();
            $("#test_form").submit();
        });
    }

    function createTable(callBack) {
        var table = document.createElement("table");
        table.setAttribute("class", "table_view");
        if ($(".option.selected").find(".icon_small_text").html() != "Inventory") {
            var orderName = $(".active").find("#order_name").html();
            table.innerHTML += "<tr class='row'><th colspan='6'>Catering Order</th></tr>";
            table.innerHTML += "<tr class='row'><th colspan='6' class='heading'>"+orderName+"</th></tr>";
        } else {
            table.innerHTML += "<tr class='row'><th colspan='8' class='heading'>Invoice</th></tr>";
        }
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                var row = document.createElement("TR");
                var cell = "";
                $(this).children(":lt(8)").each(function() {
                    if ($(this).hasClass("row_mark")) {
                        var td = document.createElement("TD");
                        if ($(this).hasClass("marked")) {
                            td.setAttribute("class", "row_mark marked");
                        } else if ($(this).hasClass("marked_warning")) {
                            td.setAttribute("class", "row_mark marked_warning");
                        } else {
                            td.setAttribute("class", "row_mark");
                        }
                        td.innerHTML = $(this).find(".text").html();
                        cell = td.outerHTML;
                    } else if ($(this).children().attr("id") == "quantity_received" || $(this).children("textarea").length > 0) {
                        var td = document.createElement("TD");
                        if ($(this).hasClass('field_warning')) {
                            td.setAttribute("class", "field_warning");
                        }
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
        table.innerHTML += "<tr><td class='table_heading' colspan='5'><h4>Total Cost</h4></td>"+
                           "<td class='table_heading' colspan='3'><h4>"+totalCost+"</h4></td></tr>";
        callBack(table);
    }

    function totalCost() {
        var totalCost = "";
        var tax = $("#sales_tax").val();
        var costSpan = document.getElementById("cost_span");
        $(".cost").each(function() {
            var value = $(this).html() != "-" ? $(this).html() : "";
            totalCost = +totalCost + +value.replace('$ ', "");
        });
        totalCost != "" ? costSpan.innerHTML = "$" + totalCost  : costSpan.innerHTML = "-";

        if (tax > 0 && totalCost != "") {
            var taxCost = (totalCost*tax/100) + totalCost;
            $("#tax_cost").html("$" + taxCost);
        } else {
            $("#tax_cost").html("-");
        }
    }

    $(document).ready(function() {

        $("#invoice_ul li:first a").each(function() {
            showInvoice($(this)[0]);
            $(this).addClass("active");
            $("#print_date span").html($(this).find("#selected_date").val());
            $("#print_date .print_table_date").html("created on " + $(this).find("#created_date").val());
        });

        $('#invoice_ul li a').click(function() {
            $('.side_nav li a').removeClass("active");
            $(this).addClass('active');
            $("#table_date_heading").html("");
            $("#print_date span").html($(this).find("#selected_date").val());
            $("#print_date .print_table_date").html("created on " + $(this).find("#created_date").val());
        });

        $('#catering_ul li a').click(function() {
            $('.side_nav li a').removeClass("active");
            $(this).addClass('active');
            $("#table_date_heading").html("Delivery Date");
            $("#print_date span").html($(this).parent().find("#order_date_format").val());
            $("#print_date .print_table_date").html($(this).parent().find("#order_date_created").html());
        });

         $("#popup_close").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
            $(".main_iframe").removeClass("blur");
        });

        $("#catering_tab").click(function() {
            $(".option").removeClass("selected");
            $(this).addClass("selected");
            $("#invoice_sidenav #heading h4").html("Tracked Orders");
            $("#invoice_ul").css("display", "none");
            $("#catering_ul").css("display", "block");
            $("#catering_ul #order_li:first a").each(function() {
                getCateringInvoice($(this)[0]);
                $(this).addClass("active");
                $("#table_date_heading").html("Delivery Date");
                $("#print_date span").html($(this).parent().find("#order_date_format").val());
                $("#print_date .print_table_date").html($(this).parent().find("#order_date_created").html());
            });
        });

        $("#invoice_tab").click(function() {
            $(".option").removeClass("selected");
            $(this).addClass("selected");
            $("#invoice_sidenav #heading h4").html("Tracked Invoices");
            $("#catering_ul").css("display", "none");
            $("#invoice_ul").css("display", "block");
            $("#invoice_ul li:first").each(function() {
                showInvoice($(this).children()[0]);
                $(this).children().addClass("active");
                $("#table_date_heading").html("");
                $("#print_date span").html($(this).find("#selected_date").val());
                $("#print_date .print_table_date").html("created on " + $(this).find("#created_date").val());
            });
        });

        $(document).on("click", ".row_mark", function() {
            if ($(this).hasClass("marked") || $(this).hasClass("marked_warning")) {
                $(this).removeClass("marked marked_warning")
                $(this).find(".text").html("not received");
                $(this).parent().find("#quantity_received").parent().removeClass("field_warning");
                $(this).parent().find("#quantity_received").val("").prop("readonly", false);
                updateQuantity($(this).parent().find("#quantity_received")[0]);
            } else if ($(this).parent().find("#quantity_delivered").html() != "-") {
                $(this).addClass("marked");
                $(this).find(".text").html("received");
                if ($(this).parent().find("#quantity_delivered").html() >= 0 &&
                    $(this).parent().find("#quantity_received").val() == "") {
                    $(this).parent().find("#quantity_received").val($(this).parent().find("#quantity_delivered").html());
                }
                $(this).parent().find("#quantity_received").prop("readonly", true);
                updateQuantity($(this).parent().find("#quantity_received")[0]);
            }
        });

     });

</script>

