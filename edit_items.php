<?php
session_start();
require_once "database/item_table.php";
require_once "database/variables_table.php";
require_once "database/timeslot_table.php";

if (isset($_POST["timeslot_name"])) {
    TimeslotTable::add_timeslot($_POST["timeslot_name"]);
}
if (isset($_POST["tab_name"])) {
    TimeslotTable::delete_timeslot($_POST["tab_name"]);
}
if (isset($_POST["checkbox"])) {
    ItemTable::delete_multiple_items($_POST["checkbox"]);
}
if (isset($_POST["base_sales"])) {
    VariablesTable::update_base_sales($_POST["base_sales"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Slots</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe">
        <div id="add_div_main" class="none">
            <div id="add_div" class="add_div">
            <div>
                <h4>Add New Item <hr></h4>
                <div class="inline">
                    <label for="new_item_name">Name</label>
                    <input class="userinput" type="text" id="new_item_name" placeholder="Item Name" required autofocus>
                </div>
                <div class="inline">
                    <label for="new_item_unit">Unit</label>
                    <input class="userinput" type="text" id="new_item_unit" placeholder="Item Unit" required>
                </div>
                <div class="inline">
                    <label for="new_item_quantity">Quantity</label>
                    <input class="userinput" type="text" id="new_item_quantity" placeholder="Item Quantity">
                </div>
                <div class="block" id="item_add_div" >
                    <input type="submit" value="Add Item" class="button button_add_drawer" id="item_add_button">
                </div>
            </div>
            </div>
            <button id="drawer_tag_item" class="drawer_tag_open">Close</button>
        </div>
        <div class="div_fade"></div>
        <div class="div_category" id="item_list_div">
            <ul class="category_list">
                <button class="button_flat inline" id="item_list_cancel">Close</button>
                <h4 class="inline">ALL ITEMS</h4><hr>
                <?php $result = ItemTable::get_items(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="list_li"><span><?php echo $row["name"]; ?></span></li>
                <?php endwhile ?>
            </ul>
        </div>

        <div class="div_table" id="items_div_table">
            <div class="div_left_tabs">
                <ul class="tab_ul">
                    <li class="tab_li"><span id="day_tab" onclick=getTab(this)><?php echo "Full Day" ?></span></li>
                </ul>
            </div>
            <div class="div_right_tabs">
                <ul class="tab_ul inline" id="timeslot_ul">
                <?php $result = TimeslotTable::get_timeslots(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="tab_div" timeslot-name="<?php echo $row['name'] ?>">
                        <li class="tab_li" ><span><?php echo $row["name"] ?></span></li>
                    </div>
                <?php endwhile ?>
                </ul>
                <input type="submit" class="tab_add_button" value="+">
                <button class="tab_delete_button" onclick=tabDelete()><img src="images/delete.png" alt="" width="25px" height="25px"></button>
            </div>

            <table class="table_view" id="table" border="1px" >
                <tr class="option_bar">
                    <th colspan="2"><button class="button_flat" id="add_item_button">Add New Item</button></th>
                    <th>
                        <div class="none" id="div_quantity_sales">
                            Quantity for sales ($)
                            <form action="edit_items.php" method="post" class="inline middle">
                                <input type="number" name="base_sales" value="<?php echo VariablesTable::get_base_sales(); ?>" onchange="this.form.submit()" class="align_center">
                            </form>
                        </div>
                    </th>
                    <th id="td_delete"></th>
                </tr>
                <tr class="tr_confirm">
                    <td class="td_checkbox"><input type="checkbox" class="item_checkbox" id="select_all"></td>
                    <td id="td_cancel">Cancel
                    <td id="td_done">Done</th>
                </tr>
                <tr>
                    <th></th>
                    <th>Item</th>
                    <th>Unit</th>
                    <th id="th_quantity">Quantity</th>
                </tr>
                <tbody id="item_tbody">
                </tbody>
            </table>
        </div>
    </div>

    <div class="div_popup_back">
        <div class="div_popup">
            <h4>New Timeslot
            <input type="button" class="popup_cancel" id="popup_cancel" value="x"><hr></h4>
            <form action="edit_items.php" method="post">
                <input type="text" id="timeslot_name" name="timeslot_name" placeholder="Name">
                <input type="submit" value="Add" class="button">
            </form>
        </div>
    </div>

    <form action="edit_items.php" method="post" id="checkbox_form"></form>
    <form action="edit_items.php" method="post">
        <input type="hidden" name="tab_name" id="tab_name">
    </form>
 </body>
 </html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script
      src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
      integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
      crossorigin="anonymous"></script>
<script>
    function  getTab(tabName) {
        $(".list_li").removeClass("selected");
        timeSlotName = tabName.innerHTML;
        if (timeSlotName == "Full Day") {
            if (!$("#delete_item").length) {
                var deleteButton = '<button id="delete_item" class="button_flat">Delete</button>';
                $("#td_delete").append(deleteButton);
            }
            $("#add_item_button").html("Add New Item");
            $("#div_quantity_sales").css("display", "block");
            $("#th_quantity").html("Quantity");
            $.post("jq_ajax.php", {getItems: ""}, function(data, status) {
                document.getElementById("item_tbody").innerHTML = data;
            });
        } else {
            $("#add_item_button").html("Item List");
            $("#div_quantity_sales").css("display", "none");
            $("#th_quantity").html("Quantity Factor");
            $("#delete_item").remove();
            $.post("jq_ajax.php", {getCategoryItemsTimeSlot: "", timeSlotName: timeSlotName}, function(data, status) {
                document.getElementById("item_tbody").innerHTML = data;
                $(".item_name").each(function() {
                    var itemName = $(this).val();
                    $(".list_li").each(function() {
                        if ($(this).children().html() == itemName) {
                            $(this).addClass("selected");
                        }
                    });
                });
            });
        }
    }

    function deleteTab(deleteButton) {
     var tabName = deleteButton.parentNode.parentNode.children[0].value;
       deleteButton.parentNode.children[1].value = tabName;
       deleteButton.parentNode.submit();
    }

    function tabDelete() {
        var tabName = $(".tab_li.selected").children().html();
        if (tabName != "Full Day") {
           if (confirm("delete '"+tabName+"' ?")) {
                document.getElementById("tab_name").value = tabName;
                document.getElementById("tab_name").parentNode.submit();
            }
        }
    }

    function factorChange(obj) {
        var factor = obj.value;
        if (factor > 1) {
            obj.value = 1;
            factor = 1;
        }
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var tsiId = document.getElementById("table").rows[rowIndex].children[4].value;
        $.post("jq_ajax.php", {UpdateTimeslotFactor: "", tsiId: tsiId, factor: factor});
    }

    function quantityChange(obj) {
        var quantity = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("table").rows[rowIndex].children[4].value;

        $.post("jq_ajax.php", {itemId: itemId, quantity: quantity});
    }

    function updateItem(obj) {
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemName = document.getElementById("table").rows[rowIndex].children[1].children[0].value;
        var itemUnit  = document.getElementById("table").rows[rowIndex].children[2].children[0].value;
        var itemId  = document.getElementById("table").rows[rowIndex].children[4].value;
        $.post("jq_ajax.php", {updateItems: "", itemName: itemName, itemUnit: itemUnit, itemId: itemId});
    }

    $(document).ready(function() {
        $(".tab_li span:first").each(function() {
           getTab($(this)[0]);
           $(this).parent().addClass("selected");
        });

        $("#timeslot_ul").sortable({
            delay: 200,
            revert: 120,
            containment: $(".div_right_tabs"),
            cursorAt: {left: 45},
            axis: "x",
            update: function(event, ui) {
                var names = $(this).sortable("toArray", {attribute: "timeslot-name"});
                $.post("jq_ajax.php", {UpdateTimeslotOrder: "", timeslotNames: names});
            }
        });

        $(".tab_li span").click(function() {
            getTab($(this)[0]);
            $(".tab_li").removeClass("selected");
            $(this).parent().addClass("selected");
        });

        $(".list_li").click(function() {
            if (timeSlotName != "Full Day") {
                $(this).toggleClass(function() {
                    if ($(this).hasClass("selected")) {
                        var itemName = $(this).children().html();
                        var timeslotName = $(".tab_li.selected").children().html();
                         $.post("jq_ajax.php", {RemoveTimeslotItem: "", itemName: itemName, timeslotName: timeslotName});
                    } else {
                        var itemName = $(this).children().html();
                        var timeslotName = $(".tab_li.selected").children().html();
                        $.post("jq_ajax.php", {AddTimeslotItem: "", itemName: itemName, timeslotName: timeslotName});
                    }
                    $.post("jq_ajax.php", {getCategoryItemsTimeSlot: "", timeSlotName: timeSlotName}, function(data, status) {
                        document.getElementById("item_tbody").innerHTML = data;
                    });
                    return "selected";
                });
            }
        });

        $(".tab_add_button").click(function() {
            $(".div_popup_back").css("display", "block");
            $(".main_iframe").addClass("blur");
        });

        $(".tab_add_button").hover(
            function() {
                $(".tab_delete_button").css({opacity: "1",
                                             transform: "translateY(30px)",
                                             border: "1px solid #F44336", "border-top": "none"});
            },  function() {
                if ($(".tab_delete_button").is(":hover")) {
                    $(".tab_delete_button").mouseleave(function() {
                        $(".tab_delete_button").css({transform: "translateY(0px)", border: "1px solid transparent", opacity: "0"});
                    });
                } else {
                    $(".tab_delete_button").css({transform: "translateY(0px)", border: "1px solid transparent", opacity: "0"});
                }
            }
        );

        $("#popup_cancel").click(function() {
            $(".main_iframe").removeClass("blur");
            $(".div_popup_back").fadeOut(190, "linear");
        });

        $("#add_item_button").click(function() {
            if ($(this).html() == "Item List") {
                $("#item_list_div").css("width", "25%");
                $(".div_table").css("width", "73%");
            } else {
                $("#add_div").slideDown(180, "linear", function() {
                    $(".div_fade").css("display", "block");
                    $("#drawer_tag_item").fadeIn(300, "linear");
                });
            }
        });

        $("#drawer_tag_item").click(function() {
            $("#drawer_tag_item").fadeOut(100, "linear");
            $("#add_div").slideUp(180, "linear");
            $(".div_fade").css("display", "none");
        });

        $(".div_fade").click(function() {
            $("#drawer_tag_item").fadeOut(100, "linear");
            $("#add_div").slideUp(180, "linear");
            $(".div_fade").css("display", "none")
        });

        $("#item_add_button").click(function() {
            var itemName = $("#new_item_name").val();
            var itemUnit = $("#new_item_unit").val();
            var itemQuantity = $("#new_item_quantity").val();

            $.post("jq_ajax.php", {addItem: "", itemName: itemName, itemUnit: itemUnit, itemQuant: itemQuantity}, function(data, status) {
                $("body").append(data);
                getTab(document.getElementById("day_tab"));
                $("userinput").trigger("reset");
            });
        });

        $(document).on("click", "#delete_item", function() {
            $(".tr_confirm").css("display", "table");
            $(".item_checkbox").css("display", "initial");
            $(".tab_li:not(.selected)").addClass("disabled");
            $(".tab_li.selected").css("pointer-events", "none");
            $(".tab_add_button").addClass("disabled");
        });

        $("#td_done").click(function() {
            document.getElementById("checkbox_form").submit();
        });

        $("#td_cancel").click(function() {
            $(".tr_confirm").css("display", "none");
            $(".item_checkbox").css("display", "none");
            $(".tab_li").removeClass("disabled");
            $(".tab_add_button").removeClass("disabled");
            $(".tab_li.selected").css("pointer-events", "initial");
        });

        $("#item_list_cancel").click(function() {
            $("#item_list_div").css("width", "0px");
            $(".div_table").css("width", "98%");
        });

        $("#select_all").change(function() {
            $("input[type='checkbox']").prop("checked", $(this).prop("checked"));
        });
    });
</script>
