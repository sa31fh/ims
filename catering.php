<?php
session_start();
require_once "mpdf/vendor/autoload.php";
require_once "database/catering_order_table.php";
require_once "database/catering_order_item_table.php";
require_once "database/catering_item_table.php";
require_once "database/recipe_table.php";

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
                    <a class="flex_col" onclick="getCateringItems(this)">
                        <span id="order_name"><?php echo $row["name"] ?></span>
                        <span id="order_date_created">created on <?php echo date("jS M Y", strtotime($row["date_created"]))?></span>
                    </a>
                    <input id="order_id" type="hidden" value="<?php echo $row["id"] ?>">
                    <input id="order_date" type="hidden" value="<?php echo $row["date_delivery"] ?>">
                    <input type="hidden" id="order_date_format" value="<?php echo date("D, jS M Y", strtotime($row["date_delivery"])); ?>">
                    <input id="order_note" type="hidden" value="<?php echo $row["notes"] ?>">
                    <input id="order_invoice" type="hidden" value="<?php echo $row["date_invoice"] ?>">
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
            <div id="add_heading"><h4></h4></div>
            <ul class="category_list display_none" id="order_items">
                <?php $result = CateringItemTable::get_items_categories($_SESSION["date"]); ?>
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
            <ul class="category_list display_none" id="order_recipes">
                <li class="list_li_category">
                    <span>Recipes</span>
                </li>
                <?php $result = RecipeTable::get_recipes($_SESSION["date"]); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list_li" id="item_list">
                        <span><?php echo $row["name"]; ?></span>
                        <input type="hidden" id="item_id" value="<?php echo $row["id"]; ?>">
                    </li>
                <?php endwhile ?>
            </ul>
        </div>

        <div class="main_top_side">
            <div class="toolbar_print"  id="catering_toolbar">
                <div class="toolbar_div flex_1">
                    <a class="option" onclick=addItems()>Add Items</a>
                </div>
                <div class="toolbar_div flex_1">
                    <a class="option" onclick=addRecipes()>Add Recipes</a>
                </div>
                <div class="toolbar_div flex_1">
                    <label class="switch">
                        <input class="switch-input" type="checkbox" onclick=checkRequired() />
                        <span class="switch-label" data-on="Required" data-off="All"></span>
                        <span class="switch-handle"></span>
                    </label>
                </div>
                <div class="toolbar_div flex_1 people_div">
                    <span>Number of people</span>
                    <input type="number" id="catering_people" name="catering_people" value=""onchange=updateCateringPeople(this)>
                </div>
                <div class="toolbar_div flex_1">
                    <a id="print_share" class="option" onclick=sendPrint()>Share</a>
                </div>
                <div class="toolbar_div flex_1">
                    <a id="print_pdf" class="option" onclick=printPdf()>PDF</a>
                </div>
                <div class="toolbar_div flex_1 invoice_send" id="div_invoice_send"onclick="trackInvoice()">
                    Send Invoice
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

    <form action="invoice.php" method="post" id="catering_invoice_form">
        <input type="hidden" name="catering_invoice_date" id="catering_invoice_date" value="">
    </form>

    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"] ?>">


    <?php $page = "catering";
    include_once "new_nav.php" ?>
</body>
</html>

<script type="text/javascript" src="jq/jquery-3.2.1.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script>

    function newOrder() {
        $("#order_form").submit();
        $("#popup_heading").html("New Order");
        $(".div_popup_back").css("display", "block");
    }

    function addItems() {
        if ($("#div_invoice_send").html() != "View Invoice") {
            $("#add_heading").children().html("add items");
            $("#order_item_list").css("display", "flex");
            $("#order_recipes").css("display", "none");
            $("#order_items").css("display", "block");
        }
    }

    function addRecipes() {
        if ($("#div_invoice_send").html() != "View Invoice") {
            $("#add_heading").children().html("add recipes");
            $("#order_item_list").css("display", "flex");
            $("#order_items").css("display", "none");
            $("#order_recipes").css("display", "block");
        }
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
        var heading = "";

        $.post("jq_ajax.php", {getCateringOrderItems: "", orderId: orderId}, function(data, status) {
            $(".print_tbody").remove();
            $("#invoice_table").append(data);
            $(".delivery_date").html($(".active").parent().find("#order_date_format").val());
            $("#created_date").html($(".active").find("#order_date_created").html());
            $(".list_li").removeClass("selected");
            $("#deleted_heading").remove();
            $("#deleted_li").remove();
            $(".item_name").each(function() {
                var itemName = $(this).html();
                $(".list_li").each(function() {
                    if ($(this).children().html() == itemName) {
                        $(this).addClass("selected");
                    }
                });
            });
            $(".recipe_item").each(function() {
                var recipeName = $(this).html();
                var recipeId = $(this).parent().find(":nth-child(5)").val();
                var exists = false;
                $("#order_recipes .list_li").each(function() {
                    if ($(this).children().html() == recipeName) {
                        exists = true;
                    }
                });
                if (!exists) {
                    if (heading == "") {
                        $("#order_recipes").append('<li class="list_li_category" id="deleted_heading"><span>Deleted Recipes</span></li>');
                    }
                    var recipe = '<li class="list_li selected" id="deleted_li">'+
                                '<span>'+recipeName+'</span>'+'<input type="hidden" id="item_id" value='+recipeId+'></li>';
                    $("#order_recipes").append(recipe);
                }
            });
            $(".div_required").each(function() {
                if ($(this).find(".span_qc").val() != "") {
                    $(this).find(".tab").trigger("click");
                }
            });
            checkRequired();
            getCateringPeople(orderId);
            checkInvoice();
        });
    }

    function getCateringPeople(orderId) {
        $.post("jq_ajax.php", {getCateringPeople: "", orderId: orderId}, function(data){
            $("#catering_people").val(data);
        });
    }

    function updateCateringPeople(obj) {
        var people = obj.value;
        people = people <= 0 ? 'NULL' : people;
        var orderId = $(".active").next().val();
           
        $.post("jq_ajax.php", {calcCateringQuantityRequired : "", people: people, orderId: orderId});
           
        $.post("jq_ajax.php", {updateCateringPeople: "", people: people, orderId: orderId}, function(status){
            if (status) {
                $(".active").trigger("click");
            }
        });
    }

    function updateQuantity(obj) {
        if (obj.value < 0) {
            obj.value = "";
        } else {
            var quantity = obj.value == "" ? "NULL" : obj.value;
            var itemId = obj.parentNode.parentNode.children[4].value;
            var orderId = $(".active").next().val();

            $.post("jq_ajax.php", {updateCateringQuantity: "", quantity: quantity, itemId: itemId, orderId: orderId});
        }
    }

    function updateRecipeQuantity(obj) {
        if (obj.value < 0) {
            obj.value = "";
        } else {
            var quantity = obj.value == "" ? "NULL" : obj.value;
            var recipeId = obj.parentNode.parentNode.children[4].value;
            var orderId = $(".active").next().val();

            $.post("jq_ajax.php", {updateCateringRecipeQuantity: "", quantity: quantity, recipeId: recipeId, orderId: orderId});
            $.post("jq_ajax.php", {updateOrderItemQuantity: "", quantity: quantity, recipeId: recipeId, orderId: orderId});
        }
    }

    function updateQuantityCustom(obj) {
        var quantity = obj.value;
        if (quantity < 0) {
            obj.value = "";
        } else {
            quantity = quantity == "" ? 'NULL' : quantity;
            var itemId = $(obj).parents("tr").find("#item_id").val();
            var orderId = $(".active").next().val();

            $.post("jq_ajax.php", {updateCateringQuantityCustom: "", quantity: quantity, itemId: itemId, orderId: orderId});
            // updateCost(obj);
        }
    }

    function updateNotes(obj) {
        var notes = obj.value;
        var itemId = obj.parentNode.parentNode.children[4].value;
        var orderId = $(".active").next().val();

        $.post("jq_ajax.php", {updateCateringNotes: "", notes: notes, itemId: itemId, orderId: orderId});
    }

    function updateRecipeNotes(obj) {
        var notes = obj.value;
        var recipeId = obj.parentNode.parentNode.children[4].value;
        var orderId = $(".active").next().val();

        $.post("jq_ajax.php", {updateCateringRecipeNotes: "", notes: notes, recipeId: recipeId, orderId: orderId});
    }

    function updateOrderNote(obj) {
        var note = obj.value;
        var orderId = $(".active").next().val();
        $(".active").parent().find("#order_note").val(note);

        $.post("jq_ajax.php", {updateOrderNote: "", note: note, orderId: orderId});
    }

    function checkInvoice() {
        if ($(".active").parent().find("#order_invoice").val() != "") {
            $("#div_invoice_send")
                .removeClass("invoice_send")
                .addClass("invoice_view")
                .html("View Invoice");
                lockInvoice();
        } else {
            $("#div_invoice_send")
                .removeClass("invoice_view")
                .addClass("invoice_send")
                .html("Send Invoice");
        }
    }

    function trackInvoice() {
        var orderId = $(".active").parent().find("#order_id").val();
        var date = $("#session_date").val();
        if ($(".active").parent().find("#order_invoice").val() != "") {
            $("#catering_invoice_date").val($(".active").parent().find("#order_invoice").val());
            $("#catering_invoice_form").submit();
        } else {
            $.post("jq_ajax.php", {updateOrderInvoiceDate: "", orderId: orderId, date: date});
            $(".active").parent().find("#order_invoice").val(date);
            $("#div_invoice_send")
                .removeClass("invoice_send")
                .addClass("invoice_view")
                .html("View Invoice");
            lockInvoice();
        }
    }

    function lockInvoice() {
        $(".div_invoice_table").find("input[type='number']").prop("readonly", "true");
        $(".div_invoice_table").find("textarea").prop("readonly", "true");
        $("#catering_people").prop("readonly", "true");
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
        table.innerHTML += "<tr class='row'><th colspan='4' class='table_title'>Waterloo</th></tr>";
        table.innerHTML += "<tr class='row'><th colspan='4' class='table_title'>Catering Order</th></tr>";
        table.innerHTML += "<tr class='row'><th colspan='4' class=:heading>"+orderName+"</th></tr>";
        $(".table_view tr").each(function() {
            if($(this).css('display') != 'none') {
                var row = document.createElement("TR");
                var cell = "";
                $(this).children(":lt(4)").each(function() {
                    if ($(this).children(".div_required").length > 0) {
                        var td = document.createElement("TD");
                        if ($(this).find(".span_qc").val() != "") {
                            td.innerHTML = $(this).find(".span_qc").val();
                        } else {
                            td.innerHTML = $(this).find(".span_qr").html();
                        }
                        cell += td.outerHTML;
                    } else if ($(this).children("textarea").length > 0) {
                        var td = document.createElement("TD");
                        td.innerHTML = $(this).children().val();
                        cell += td.outerHTML;
                    } else if ($(this).children("#quantity_delivered").length > 0) {
                        var td = document.createElement("TD");
                        td.innerHTML = $(this).find("#quantity_delivered").val();
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
            $("#catering_people").prop("readonly", false);
        });

        $(".popup_close").click(function() {
            $(".div_popup_back").fadeOut(190, "linear");
            getCateringItems($(".active")[0]);
            $("#popup_frame").contents().find("body").html('');
        });

        $(document).on("click", ".list_li", function() {
            orderId = $(".active").next().val();
            itemId = $(this).find("#item_id").val();
            if ($(this).parent().attr("id") == "order_items") {
                $(this).toggleClass(function() {
                    if ($(this).hasClass("selected")) {
                        $.post("jq_ajax.php", {removeCateringItem: "", itemId: itemId, orderId: orderId});
                    } else {
                        $.post("jq_ajax.php", {addCateringOrderItem: "", itemId: itemId, orderId: orderId});
                    }
                    $.post("jq_ajax.php", {getCateringOrderItems: "", orderId: orderId}, function(data, status) {
                        $(".print_tbody").remove();
                        $("#invoice_table").append(data);
                    });
                    return "selected";
                });
            } else {
                $(this).toggleClass(function() {
                    if ($(this).hasClass("selected")) {
                        $.post("jq_ajax.php", {removeCateringRecipe: "", itemId: itemId, orderId: orderId});
                        $.post("jq_ajax.php", {removeOrderRecipeItems: "", recipeId: itemId, orderId: orderId});
                    } else {
                        $.post("jq_ajax.php", {addCateringRecipe: "", itemId: itemId, orderId: orderId});
                        $.post("jq_ajax.php", {updateOrderRecipeItems: "", recipeId: itemId, orderId: orderId});
                    }
                    $.post("jq_ajax.php", {getCateringOrderItems: "", orderId: orderId}, function(data, status) {
                        $(".print_tbody").remove();
                        $("#invoice_table").append(data);
                    });
                    return "selected";
                });
            }
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

        $(document).on("click", ".tab", function() {
            $(this).parent().find(".selected").removeClass("selected");
            $(this).addClass("selected");
            if ($(this).attr("id") == "calculated") {
                $(this).parents("td").find(".span_qc").css("display", "none");
                $(this).parents("td").find(".span_qr").css("display", "block");
                $(this).parents("td").find("#heading").html("calculated value");
                // updateCost($(this).parents("td").find(".span_qr")[0]);
            } else {
                $(this).parents("td").find(".span_qr").css("display", "none");
                $(this).parents("td").find(".span_qc").css("display", "block");
                $(this).parents("td").find("#heading").html("custom value");
                // updateCost($(this).parents("td").find(".span_qc")[0]);
            }
        });
    });
</script>