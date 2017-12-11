<?php
session_start();
require_once "database/item_table.php";
require_once "database/variables_table.php";
require_once "database/timeslot_table.php";
require_once "database/item_required_days_table.php";

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
?>
    <script>
        window.parent.location.href = window.parent.location.href;
    </script>
<?php
exit();
}
$_SESSION["last_activity"] = time();

if (isset($_POST["timeslot_name"])) {
    TimeslotTable::add_timeslot($_POST["timeslot_name"]);
}
if (isset($_POST["tab_name"])) {
    TimeslotTable::delete_timeslot($_POST["tab_name"]);
}
if (isset($_POST["checkbox"])) {
    ItemTable::delete_multiple_items($_POST["checkbox"], $_SESSION["date"]);
}
if (isset($_POST["base_sales"])) {
    VariablesTable::update_base_sales($_POST["base_sales"]);
}
if (isset($_POST["sales_tax"])) {
    VariablesTable::update_sales_tax($_POST["sales_tax"]);
}
$item_table = ItemTable::get_items_categories($_SESSION["date"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Slots</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe">
        <div id="add_div_main" class="none font_open_sans">
            <div id="add_div" class="add_div">
                <div>
                    <h4>Add New Item</h4>
                    <div class="inline">
                        <label for="new_item_name">Name</label>
                        <input class="userinput" type="text" id="new_item_name" placeholder="Required" required autofocus>
                    </div>
                    <div class="inline">
                        <label for="new_item_unit">Unit</label>
                        <input class="userinput" type="text" id="new_item_unit" placeholder="Required" required>
                    </div>
                    <div class="inline">
                        <label for="new_item_quantity">Quantity</label>
                        <input class="userinput" type="number" id="new_item_quantity" placeholder="Required" required>
                    </div>
                    <div class="inline">
                        <label for="new_item_price">Price</label>
                        <input class="userinput" type="number" id="new_item_price" placeholder="Optional">
                    </div>
                    <div class="inline">
                        <label for="new_item_deviation">Deviation</label>
                        <input class="userinput" type="number" id="new_item_deviation" placeholder="Optional">
                    </div>
                    <div class="block" id="item_add_div" >
                        <input type="submit" value="Add Item" class="button button_add_drawer" id="item_add_button">
                    </div>
                </div>
            </div>
            <button id="drawer_tag_item" class="drawer_tag_open">Close</button>
        </div>
        <div class="div_fade"></div>

       <div class="div_category font_open_sans" id="item_list_div">
            <div class="popup_titlebar">
                <span class="popup_close" id="item_list_cancel"></span>
            </div>
            <div><h4>All Items</h4></div>
            <ul class="category_list">
                <?php $current_category = 1; ?>
                <?php while ($row = $item_table->fetch_assoc()): ?>
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
                    <li class="list_li" id="item_list"><span><?php echo $row["name"]; ?></span></li>
                <?php endwhile ?>
            </ul>
        </div>

        <div class="div_table font_roboto" id="items_div_table">
            <div class="div_child" id="inventory_tabs">
                <div class="div_left_tabs">
                    <ul class="tab_ul">
                        <li class="tab_li selected"><span id="day_tab" onclick=getTab(this)><?php echo "Full Day" ?></span></li>
                    </ul>
                </div>
                <div class="div_right_tabs">
                    <ul class="tab_ul inline" id="timeslot_ul">
                    <?php $result = TimeslotTable::get_timeslots(); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="tab_div" timeslot-name="<?php echo $row['name'] ?>">
                            <li class="tab_li" ><span onclick=getTab(this)><?php echo $row["name"] ?></span></li>
                        </div>
                    <?php endwhile ?>
                    </ul>
                    <button class="tab_delete_button entypo-trash" onclick=tabDelete()></button>
                    <button class="tab_add_button entypo-plus"></button>
                </div>
            </div>
            <div id="div_print_table">
                <table class="table_view" id="item_table_view" border="1px" >
                    <tr class="option_bar">
                        <th colspan="2" id="button_th">
                            <button class="button_flat entypo-plus" id="add_item_button">Add</button>
                            <div class="divider"></div>
                            <button class="button_flat entypo-trash" id="delete_item">Delete</button>
                        </th>
                        <th colspan="2" id="th_sales">
                            <div class="none" id="div_quantity_sales">
                                Quantity for sales
                                <form action="edit_items.php" method="post" class="inline">
                                    <span>$</span>
                                    <input type="number" name="base_sales" value="<?php echo VariablesTable::get_base_sales(); ?>" onchange="this.form.submit()" class="align_center">
                                </form>
                            </div>
                        </th>
                        <th colspan="2">
                            <div id="div_sales_tax">
                                Sales Tax
                                <form action="edit_items.php" method="post" class="inline">
                                    <span>%</span>
                                    <input type="number" name="sales_tax" value="<?php echo VariablesTable::get_sales_tax(); ?>" onchange="this.form.submit()">
                                </form>
                            </div>
                        </th>
                        <th >
                            <input class="search_bar" id="search_bar" type="search" placeholder="search" oninput=searchBar(this)>
                        </th>
                    </tr>
                    <tr class="tr_confirm">
                        <td class="td_checkbox">
                            <div class="checkbox">
                                <input type="checkbox" class="item_checkbox" id="select_all">
                                <span class="checkbox_style"></span>
                            </div>
                        </td>
                        <td id="td_cancel">Cancel
                        <td id="td_done">Done</th>
                    </tr>
                    <tr>
                        <th id="buffer"></th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th id="th_quantity">Quantity</th>
                        <th id="th_price">Price</th>
                        <th id="th_deviation">Deviation</th>
                        <th id="th_rounding">Rounding</th>
                    </tr>
                    <tbody id="item_tbody">
                    <?php  $current_category = 1;?>
                    <?php mysqli_data_seek($item_table, 0); ?>
                    <?php  while($row = $item_table->fetch_assoc()): ?>
                    <?php  if ($row["category_name"] != $current_category AND $row["category_name"] != null): ?>
                            <?php $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="7" class="table_heading"><?php echo $row["category_name"]?><span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php elseif ($row["category_name"] != $current_category AND $row["category_name"] == null): ?>
                    <?php  $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="7" class="table_heading">Uncategorized Items<span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php endif ?>
                        <tr>
                            <td class="td_drawer">
                                <div class="div_tray">
                                <?php
                                    $day_ids = [];
                                    $result = ItemRequiredDaysTable::get_item_days($row["id"]);
                                    while ($item_row = $result->fetch_array()) {
                                        $day_ids[]= $item_row[0];
                                    }
                                ?>
                                    <span value="7" class="<?php echo in_array('7', $day_ids) ? 'active' : ''?>">Sun</span>
                                    <span value="1" class="<?php echo in_array('1', $day_ids) ? 'active' : ''?>">Mon</span>
                                    <span value="2" class="<?php echo in_array('2', $day_ids) ? 'active' : ''?>">Tue</span>
                                    <span value="3" class="<?php echo in_array('3', $day_ids) ? 'active' : ''?>">Wed</span>
                                    <span value="4" class="<?php echo in_array('4', $day_ids) ? 'active' : ''?>">Thu</span>
                                    <span value="5" class="<?php echo in_array('5', $day_ids) ? 'active' : ''?>">Fri</span>
                                    <span value="6" class="<?php echo in_array('6', $day_ids) ? 'active' : ''?>">Sat</span>
                                    <div class="close"></div>
                                </div>
                                <?php unset($day_ids); ?>
                            </td>
                            <input type="hidden" class="item_id" name="item_id" value="<?php echo $row["id"]?>">
                            <td class="td_checkbox">
                                <div class="checkbox">
                                    <input type="checkbox" class="item_checkbox" name="checkbox[]" value="<?php echo $row["id"]?>" form="checkbox_form">
                                    <span class="checkbox_style"></span>
                                </div>
                                <div class="calendar">
                                    <span class="fa-calendar-plus-o"></span>
                                </div>
                            </td>
                            <td><input type="text" name="item_name" value="<?php echo $row["name"]?>" onchange=updateItem(this) class="align_center item_name"></td>
                            <td><input type="text" name="item_unit" value="<?php echo $row["unit"]?>" onchange=updateItem(this) class="align_center"></td>
                            <td><input type="number" name="item_quantity" step="any" min="0" value="<?php echo $row["quantity"]?>" onchange=quantityChange(this) class="align_center number_view"></td>
                            <td>$<input type="number" name="item_price" step="any" min="0" value="<?php echo $row["price"]?>" onchange=updateItem(this) class="align_center number_view"></td>
                            <td><input type="number" name="item_deviation step="1" min="0" value="<?php echo $row["deviation"]?>" onchange=updateItemDeviation(this) class="align_center number_view">%</td>
                            <td id="round_tr">
                                <select name="" id="" onchange=updateRoundingOption(this)>
                                    <option value="none" <?php echo $row["rounding_option"] == "none" ? "selected" : ""?> >none</option>
                                    <option value="up" <?php echo $row["rounding_option"] == "up" ? "selected" : "" ?> >up</option>
                                    <option value="down" <?php echo $row["rounding_option"] == "down" ? "selected" : ""?> >down</option>
                                </select>
                                <input id="round_input" type="number" step="any" value="<?php echo $row["rounding_factor"]?>" onchange=updateRoundingFactor(this)
                                        class="align_center" <?php echo $row["rounding_option"] == "none" ? "disabled" : ""?> >
                            </td>
                        </tr>
                    <?php endwhile ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="div_popup_back">
        <div class="div_popup popup_add_timeslot">
             <div class="popup_titlebar">
                <span class="popup_close"></span>
                <span id="title_name">new timeslot</span>
            </div>
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
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script
      src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
      integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
      crossorigin="anonymous"></script>
<script src="touch_punch.js"></script>
<script>
    function  getTab(tabName) {
        $(".list_li").removeClass("selected");
        timeSlotName = tabName.innerHTML;
        if (timeSlotName == "Full Day") {
            $.post("jq_ajax.php", {getItems: ""}, function(data, status) {
                document.getElementById("item_tbody").innerHTML = data;
                $("#add_item_button").html("Add");
                if (!$("#delete_item").length) {
                    var deleteButton = '<button id="delete_item" class="button_flat entypo-trash">Delete</button>';
                    $("#button_th").append(deleteButton);
                }
                $("#th_sales").attr("colspan", "4");
                $("#div_quantity_sales").css("display", "block");
                $("#th_quantity").html("Quantity");
                $("#th_deviation").css("display", "table-cell");
                $("#th_price").css("display", "table-cell");
                $("#th_rounding").css("display", "table-cell");
                $(".divider").show();
            });
        } else {
            $.post("jq_ajax.php", {getCategoryItemsTimeSlot: "", timeSlotName: timeSlotName}, function(data, status) {
                document.getElementById("item_tbody").innerHTML = data;
                $("#div_quantity_sales").css("display", "none");
                $("#th_sales").attr("colspan", "1");
                $("#th_rounding").css("display", "none");
                $("#th_price").css("display", "none");
                $("#th_deviation").css("display", "none");
                $("#delete_item").remove();
                $("#add_item_button").html("Item List");
                $(".divider").hide();
                $("#th_quantity").html("Equation");
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

    function updateRoundingOption(obj) {
        var option = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[1].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[3].children[0].value;

        $.post("jq_ajax.php", {updateRoundingOption: "", roundingOption: option, itemId: itemId}, function(data) {
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
                .error("Rounding Option for Item '"+itemName+"' did not save. Click here to try again", function(event) {
                    updateRoundingOption(obj);
                });
        });

        if (option == "none") {
            $(obj).next().attr("disabled", "disabled");
        } else {
            $(obj).next().removeAttr("disabled");
        }
    }

    function updateRoundingFactor(obj) {
        var factor = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[1].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[3].children[0].value;

        $.post("jq_ajax.php", {updateRoundingFactor: "", roundingFactor: factor, itemId: itemId}, function(data) {
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
                .error("Rounding Factor for Item '"+itemName+"' did not save. Click here to try again", function(event) {
                    updateRoundingFactor(obj);
                });
        });
    }

    function updateItemDeviation(obj) {
        var deviation = obj.value;
        if (deviation < 0 ) {
            deviation = Math.abs(obj.value);
            obj.value = deviation;
        }
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[1].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[3].children[0].value;

        $.post("jq_ajax.php", {updateItemDeviation: "", deviation: deviation, itemId: itemId}, function(data) {
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
                .error("Deviation for Item '"+itemName+"' did not save. Click here to try again", function(event) {
                    updateItemDeviation(obj);
                });
        });
    }

    function tabDelete() {
        var tabName = $(".tab_li.selected").children().html();
        if (tabName != "Full Day") {
            alertify.confirm("Delete tab '"+tabName+"' ?", function () {
                document.getElementById("tab_name").value = tabName;
                document.getElementById("tab_name").parentNode.submit();
            });
        }
    }

    function factorChange(obj) {
        var factor = obj.value;
        var eqTest = factor.replace(/x/gi, 1);
        try {
            eval(eqTest);
            factor = factor.toLowerCase();
            var rowIndex = obj.parentNode.parentNode.rowIndex;
            var tsiId = document.getElementById("item_table_view").rows[rowIndex].children[4].value;
            $.post("jq_ajax.php", {UpdateTimeslotFactor: "", tsiId: tsiId, factor: factor});
        } catch (e) {
            var erMessage = "<div class='error'> Incorrect Equation </div>";
            $("body").append(erMessage);
            $(obj, this).addClass("incorrect");
        }
    }

    function quantityChange(obj) {
        var quantity = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[1].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[3].children[0].value;

        $.post("jq_ajax.php", {updateItemQuantity: "", itemId: itemId, quantity: quantity}, function(data) {
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
                .error("Quantity for Item '"+itemName+"' did not save. Click here to try again", function(event) {
                    quantityChange(obj);
                });
        });
    }

    function updateItem(obj) {
        var row =document.getElementById("item_table_view").rows[obj.parentNode.parentNode.rowIndex];
        var itemName = row.children[3].children[0].value;
        var itemUnit  = row.children[4].children[0].value;
        var itemPrice  = row.children[6].children[0].value;
        var itemId  = row.children[1].value;
        $.post("jq_ajax.php", {updateItems: "", itemName: itemName, itemUnit: itemUnit, itemId: itemId, itemPrice: itemPrice}, function(data) {
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
                .error("Changes for Item '"+itemName+"' did not save. Click here to try again", function(event) {
                    updateItem(obj);
                });
        });
    }

    function searchBar(obj) {
        var searchText = new RegExp(obj.value, "i");
        if (obj.value != "") {
            $("#item_tbody").children().hide();
            $(".item_name").each(function() {
                var val = $(this).val();
                if (val.search(searchText) > -1) {
                    $(this).parent().parent().show();
                }
            });
        } else {
            $("#item_tbody").children().show();
        }
    }

    $(document).ready(function() {

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

        $(".list_li_category").click(function() {
            $(this).nextUntil(".list_li_category").toggle();
            if ($(this).find(".arrow_down").hasClass("up")) {
                $(this).find(".arrow_down").removeClass("up").css("transform", "rotate(45deg)");
            } else {
                $(this).find(".arrow_down").addClass("up").css("transform", "rotate(225deg)")
            }
        });

        $(document).on("click", ".item_category_tr", function() {
            $(this).nextUntil(".item_category_tr").toggle();
            if ($(this).find("span").hasClass("up")) {
                $(this).find("span").removeClass("up").css("transform", "rotate(45deg)");
            } else {
                $(this).find("span").addClass("up").css("transform", "rotate(225deg)")
            }
        });

        $(".tab_add_button").click(function() {
            $(".div_popup_back").fadeIn(120, "linear");
        });

        $(".popup_close").click(function() {
            $(".div_popup_back").fadeOut(120, "linear");
        });

        $("#add_item_button").click(function() {
            if ($(this).html() == "Item List") {
                $("#item_list_div").css({"flex": "1","max-width": "initial", "display": "flex"});
            } else {
                $("#add_div").slideDown(180, "linear", function() {
                    $(".div_fade").css("display", "block");
                    $("#drawer_tag_item").fadeIn(300, "linear");
                    $("#drawer_tag_item").css("display", "inline-block");
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
            var itemPrice = $("#new_item_price").val() == "" ? 'NULL' : $("#new_item_price").val();
            var itemDeviation = $("#new_item_deviation").val() == "" ? 'NULL' : $("#new_item_deviation").val();

            if (itemName != "" && itemUnit != "" && itemQuantity != "") {
                $.post("jq_ajax.php", {addItem: "", itemName: itemName, itemUnit: itemUnit, itemQuant: itemQuantity,
                                       itemPrice: itemPrice, itemDeviation: itemDeviation}, function(data, status) {
                    if (data == "item added") {
                        alertify
                            .delay(2500)
                            .success("Item added successfully");
                        getTab(document.getElementById("day_tab"));
                        $("userinput").trigger("reset");
                    } else if (data == "item exists") {
                        alertify
                            .delay(2500)
                            .log("Item name already exists");
                    } else {
                        alertify
                            .delay(2500)
                            .error("Process failed. Try again");
                    }
                });
            } else {
                alertify
                    .delay(3000)
                    .error("Fill all required fields");
            }
        });

        $(document).on("click", "#delete_item", function() {
            $(".calendar").css("display", "none");
            $(".tr_confirm").css("display", "table");
            $(".checkbox").css("display", "block");
            $(".tab_li:not(.selected)").addClass("disabled");
            $(".tab_li.selected").css("pointer-events", "none");
            $(".tab_add_button").addClass("disabled");
        });

        $("#td_done").click(function() {
            document.getElementById("checkbox_form").submit();
        });

        $("#td_cancel").click(function() {
            $(".tr_confirm").css("display", "none");
            $(".checkbox").css("display", "none");
            $(".calendar").css("display", "block");
            $(".tab_li").removeClass("disabled");
            $(".tab_add_button").removeClass("disabled");
            $(".tab_li.selected").css("pointer-events", "block");
        });

        $("#item_list_cancel").click(function() {
            $("#item_list_div").css({"flex": "0","max-width": "0", "diplay": "none"});
        });

        $("#select_all").change(function() {
            $("input[type='checkbox']").prop("checked", $(this).prop("checked"));
        });

        $(document).on("click", "input.incorrect", function() {
            $(this).removeClass("incorrect");
        });

        $(document).on("click", ".calendar", function() {
            $(".td_drawer").removeClass("calendar_visible");
            var ele = $(this);
            $(this).parents("tr").find(".td_drawer").css("display", "table-cell").unbind("transitionend");
            setTimeout(function() {
                ele.parents("tr").find(".td_drawer").addClass("calendar_visible");
            }, 10);
        });

        $(document).on("click", ".close", function() {
            $(this).parents(".td_drawer").removeClass("calendar_visible");
            $(".td_drawer").on("transitionend", function() {
                $(this).css("display", "none");
            });
        });

        $(document).click(function(event) {
            if(!$(event.target).closest('.td_drawer').length && !$(event.target).is(".calendar span")) {
                if ($(".td_drawer").hasClass("calendar_visible")) {
                    event.stopImmediatePropagation();
                    $(".td_drawer").removeClass("calendar_visible");
                    $(".td_drawer").on("transitionend", function() {
                        $(this).css("display", "none");
                    });
                }
            }
        });

        $(document).on("click", ".td_drawer span", function() {
            var itemId = $(this).parents("tr").find(".item_id").val();
            var dayId = $(this).attr("value");
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
                $.post("jq_ajax.php", {removeItemRequiredDay: "", itemId: itemId, dayId: dayId});
            } else {
                $(this).addClass("active");
                $.post("jq_ajax.php", {addItemRequiredDay: "", itemId: itemId, dayId: dayId});
            }
        });
    });
</script>
