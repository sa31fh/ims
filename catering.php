<?php
session_start();
require_once "mpdf/vendor/autoload.php";
require_once "database/catering_order_table.php";
require_once "database/catering_item_table.php";
require_once "database/item_table.php";

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
$_SESSION["last_activity"] = time();

if (isset($_POST["table_data"])) {
    $mpdf = new mPDF("", "A4", 0, 'roboto', 0, 0, 0, 0, 0, 0);
    $stylesheet = file_get_contents("css/pdf_styles.css");
    $mpdf->useSubstitutions=false;
    $mpdf->simpleTables = true;
    $mpdf->WriteHtml($stylesheet, 1);
    $mpdf->WriteHtml($_POST["table_data"], 2);
    $mpdf->Output($_POST["table_name"]." - ".$_POST["table_date"].".pdf", "D");
}
if (isset($_POST["delete_order_id"])) {
    CateringOrderTable::delete_order($_POST["delete_order_id"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catering</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden font_open_sans">
    <div class="main overflow_hidden">
        <div class="sidenav" id="catering_sidenav">
            <div id="heading"><h4>Orders</h4></div>
            <ul class="side_nav">
      <?php $result = CateringOrderTable::get_orders();
            $delivery_date = 1;
            while ($row = $result->fetch_assoc()): ?>
            <?php if ($row["date_delivery"] != $delivery_date): ?>
              <?php $delivery_date = $row["date_delivery"];?>
                    <li class="heading">
                        <span><?php echo date("jS M Y, l", strtotime($row["date_delivery"])); ?></span>
                    </li>
            <?php endif ?>
                <li id="order_li">
                    <a onclick="getCateringItems(this)">
                        <span id="order_name"><?php echo $row["name"] ?></span>
                        <span id="order_date_created">created on <?php echo date("jS M Y", strtotime($row["date_created"]))?></span>
                    </a>
                    <input id="order_id" type="hidden" value="<?php echo $row["id"] ?>">
                    <input id="order_date" type="hidden" value="<?php echo $row["date_delivery"] ?>">
                    <input type="hidden" id="order_date_format" value="<?php echo date("D, jS M Y", strtotime($row["date_delivery"])); ?>">
                    <input id="order_note" type="hidden" value="<?php echo $row["notes"] ?>">
                </li>
            <?php endwhile ?>
            </ul>
            <div class="toolbar_print">
                <div class="toolbar_div option" onclick="deleteOrder()">
                   <span class="icon_small entypo-trash"></span>
                   <span class="icon_small_text">delete</span>
                </div>
                <div class="toolbar_div option">
                    <button class="entypo-plus button_round" onclick=newOrder()></button>
                </div>
                <div class="toolbar_div option" onclick="editOrder()">
                    <span class="icon_small fa-edit"></span>
                    <span class="icon_small_text">edit</span>
                </div>
            </div>
        </div>

        <div class="div_category font_open_sans" id="order_item_list">
            <div class="popup_titlebar">
                <span class="popup_close" id="item_list_cancel"></span>
            </div>
            <div><h4>All Items</h4></div>
            <ul class="category_list">
                <?php $result = ItemTable::get_items_categories(); ?>
                <?php $current_category = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php if ($row["category_name"] != $current_category AND $row["category_name"] != null): ?>
                       <?php $current_category = $row["category_name"];?>
                        <li class="list_li_category">
                            <span><?php echo $row["category_name"]; ?></span>
                            <span class="arrow_down float_right collapse_arrow"></span>
                        </li>
                    <?php endif ?>
                    <?php if ($row["category_name"] != $current_category AND $row["category_name"] == null): ?>
                       <?php $current_category = $row["category_name"]; ?>
                        <li class="list_li_category">
                            <span><?php echo "Uncategorized"; ?></span>
                            <span class="arrow_down float_right collapse_arrow"></span>
                        </li>
                    <?php endif ?>
                    <li class="list_li" id="item_list">
                        <span><?php echo $row["name"]; ?></span>
                        <input type="hidden" id="item_id" value="<?php echo $row["id"]; ?>">
                    </li>
                <?php endwhile ?>
            </ul>
        </div>

        <div class="main_top_side">
            <div class="toolbar_print"  id="invoice_toolbar">
                <div class="toolbar_div">
                    <a class="option" onclick=addItems()>Add Items</a>
                </div>
                <div class="divider"></div>
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
            </div>

            <div class="div_invoice_table">
                <table class="table_view" id="invoice_table">
                    <tr id="print_date" class="row">
                        <th colspan="4">
                            <div class="heading" id="table_date_heading">Delivery Date</div>
                            <div class="delivery_date"></div>
                            <div class="print_table_date" id="created_date"></div>
                        </th>
                    </tr>
                </table>
                <div>
                    <span class="note_heading entypo-pencil">Special Instructions</span>
                    <textarea  id="note_text" class="note_text" onchange=updateOrderNote(this) placeholder="Add Special Instructions to Order"></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="div_popup_back">
        <div class="div_popup popup_order">
            <div class="popup_titlebar">
                <span id="popup_heading"></span>
                <span class="popup_close" id="popup_close"></span>
            </div>
            <iframe id="popup_frame" name="popup_frame" src="" frameborder="0"></iframe>
        </div>
    </div>

    <form action="catering_order.php" method="post" id="order_edit" target="popup_frame">
        <input type="hidden" id="edit_order_name" name="edit_order_name">
        <input type="hidden" id="edit_order_date" name="edit_order_date">
        <input type="hidden" id="edit_order_id" name="edit_order_id">
    </form>

    <form action="compose_messages.php" method="post" id="print_form" target="popup_frame">
        <input type="hidden" id="print_table_date" name="print_table_date">
        <input type="hidden" id="print_table_name" name="print_table_name">
        <input type="hidden" id="new_print_data" name="new_print_data">
    </form>

    <form action="catering.php" method="post" id="test_form" name="test_form">
        <input type="hidden" id="table_data" name="table_data">
        <input type="hidden" id="table_date" name="table_date">
        <input type="hidden" id="table_name" name="table_name">
    </form>

    <form id="order_form" action="catering_order.php" method="post" target="popup_frame"></form>
    <form id="item_form" action="catering_items.php" method="post" target="popup_frame">
        <input type="hidden" id="send_id" name="order_id">
    </form>

    <form action="catering.php" method="post" id="delete_form">
        <input type="hidden" id="delete_order_id" name="delete_order_id">
    </form>

    <?php $page = "catering";
    include_once "new_nav.php" ?>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script>

    function newOrder() {
        $("#order_form").submit();
        $("#popup_heading").html("New Order");
        $(".div_popup_back").css("display", "block");
    }

    function addItems() {
        $("#order_item_list").css("display", "flex");
    }

    function editOrder() {
        $("#edit_order_name").val($(".active").children("#order_name").html());
        $("#edit_order_date").val($(".active").parent().find("#order_date").val());
        $("#edit_order_id").val($(".active").parent().find("#order_id").val());
        $("#order_edit").submit();
        $("#popup_heading").html("Edit Order");
        $(".div_popup_back").css("display", "block");
    }

    function deleteOrder() {
        $("#delete_order_id").val($(".active").parent().find("#order_id").val());
        alertify.confirm("Delete Order '"+$(".active").children("#order_name").html()+"' ?", function() {
            $("#delete_form").submit();
        });
    }

    function getCateringItems(obj) {
        var orderId = obj.parentNode.children[1].value;

        $.post("jq_ajax.php", {getCateringItems: "", orderId: orderId}, function(data, status) {
            $(".print_tbody").remove();
            $("#invoice_table").append(data);
            $(".delivery_date").html($(".active").parent().find("#order_date_format").val());
            $("#created_date").html($(".active").find("#order_date_created").html());
            $(".list_li").removeClass("selected");
            $(".item_name").each(function() {
                var itemName = $(this).html();
                $(".list_li").each(function() {
                    if ($(this).children().html() == itemName) {
                        $(this).addClass("selected");
                    }
                });
            });
            checkRequired();
        });
    }

    function updateQuantity(obj) {
        var quantity = obj.value;
        var itemId = obj.parentNode.parentNode.children[4].value;
        var orderId = $(".active").next().val();

        $.post("jq_ajax.php", {updateCateringQuantity: "", quantity: quantity, itemId: itemId, orderId: orderId});
    }

    function updateNotes(obj) {
        var notes = obj.value;
        var itemId = obj.parentNode.parentNode.children[4].value;
        var orderId = $(".active").next().val();

        $.post("jq_ajax.php", {updateCateringNotes: "", notes: notes, itemId: itemId, orderId: orderId});
    }

    function updateOrderNote(obj) {
        var note = obj.value;
        var orderId = $(".active").next().val();
        $(".active").parent().find("#order_note").val(note);

        $.post("jq_ajax.php", {updateOrderNote: "", note: note, orderId: orderId});
    }

    function checkRequired() {
        if ($(".switch-input").prop("checked")) {
            $(".print_tbody").each(function() {
                var total = $(this).find("tr > input").length;
                var remove = 0;
                $(this).find("tr input").each(function() {
                  if ((this.value <=0 || this.value == "") && $(this).parent().nextAll("#td_notes").children("textarea").val() == "") {
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
            document.getElementById("print_table_name").value = "Catering Order - "+$(".active").find("#order_name").html();
            document.getElementById("print_table_date").value = $("#print_date").find(".delivery_date").html();
            $(".div_popup_back").css("display", "block");
            $("#popup_heading").html("Send Message");
            $("#print_form").submit();
        });
    }

    function printPdf() {
        createTable(function(table) {
            $("#table_data").val(table.outerHTML);
            document.getElementById("table_name").value = "Catering Order - "+$(".active").find("#order_name").html();
            document.getElementById("table_date").value = $("#print_date").find(".delivery_date").html();
            $("#test_form").submit();
        });
    }

    function createTable(callBack) {
        var table = document.createElement("table");
        var orderName = $(".active").find("#order_name").html();
        table.setAttribute("class", "table_view");
        table.innerHTML += "<tr class='row'><th colspan='4' class='table_title'>Catering Order</th></tr>";
        table.innerHTML += "<tr class='row'><th colspan='4' class=:heading>"+orderName+"</th></tr>";
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
        var note = $(".active").parent().find("#order_note").val() == "" ? "No Special Instructions Added" : $(".active").parent().find("#order_note").val();
        table.innerHTML += '<tr id="category"><td colspan="4" class="table_title">Special Instructions</td></tr>';
        table.innerHTML +=  '<tr id="column_data" class="row" colspan="4"><td class="order_note" colspan="4">'+note+'</td>'
        callBack(table);
    }

    $(document).ready(function() {

        $(".side_nav #order_li:first").each(function() {
            $("#note_text").html($(this).find("#order_note").val());
            getCateringItems($(this).children()[0]);
            $(this).children("a").addClass("active");
        });

        $('.side_nav li a').click(function() {
            $("#note_text").html($(this).parent().find("#order_note").val());
            $("#note_text").val($(this).parent().find("#order_note").val());
            $('.side_nav li a').removeClass("active");
            $(this).addClass('active');
        });

        $(".popup_close").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
            getCateringItems($(".active")[0]);
            $("#popup_frame").contents().find("body").html('');
        });

        $(".list_li").click(function() {
            orderId = $(".active").next().val();
            itemId = $(this).find("#item_id").val();
            $(this).toggleClass(function() {
                if ($(this).hasClass("selected")) {
                    $.post("jq_ajax.php", {removeCateringItem: "", itemId: itemId, orderId: orderId});
                } else {
                    $.post("jq_ajax.php", {addCateringItem: "", itemId: itemId, orderId: orderId});
                }
                $.post("jq_ajax.php", {getCateringItems: "", orderId: orderId}, function(data, status) {
                    $(".print_tbody").remove();
                    $("#invoice_table").append(data);
                });
                return "selected";
            });
        });

        $(".list_li_category").click(function() {
            $(this).nextUntil(".list_li_category").toggle();
            if ($(this).find(".arrow_down").hasClass("up")) {
                $(this).find(".arrow_down").removeClass("up").css("transform", "rotate(45deg)");
            } else {
                $(this).find(".arrow_down").addClass("up").css("transform", "rotate(225deg)")
            }
        });

        $("#item_list_cancel").click(function() {
            $("#order_item_list").css("display", "none");
        });
    });
</script>