<?php
session_start();
require_once "database/item_table.php";
require_once "database/variables_table.php";
require_once "database/timeslot_table.php";

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

        <div class="div_category font_open_sans" id="item_list_div">
            <ul class="category_list">
                <button class="button_flat inline" id="item_list_cancel">Close</button>
                <h4 class="inline">ALL ITEMS</h4><hr>
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
                    <li class="list_li" id="item_list"><span><?php echo $row["name"]; ?></span></li>
                <?php endwhile ?>
            </ul>
        </div>

        <div class="div_table font_roboto" id="items_div_table">
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
                <button class="tab_delete_button" onclick=tabDelete()><img src="images/delete.png" alt="" width="25px" height="25px"></button>
                <input type="submit" class="tab_add_button" value="+">
            </div>

            <table class="table_view" id="table" border="1px" >
                <tr class="option_bar">
                    <th colspan="2" id="button_th">
                        <button class="button_flat entypo-plus" id="add_item_button">Add</button>
                        <div class="divider"></div>
                        <button class="button_flat entypo-trash" id="delete_item">Delete</button>
                    </th>
                    <th colspan="4" id="th_sales">
                        <div class="none" id="div_quantity_sales">
                            Quantity for sales
                            <form action="edit_items.php" method="post" class="inline middle">
                            <span>$</span>
                                <input type="number" name="base_sales" value="<?php echo VariablesTable::get_base_sales(); ?>" onchange="this.form.submit()" class="align_center">
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
                </tbody>
            </table>
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
        var itemId = document.getElementById("table").rows[rowIndex].children[0].value;

        $.post("jq_ajax.php", {updateRoundingOption: "", roundingOption: option, itemId: itemId});

        if (option == "none") {
            $(obj).next().attr("disabled", "disabled");
        } else {
            $(obj).next().removeAttr("disabled");
        }
    }

    function updateRoundingFactor(obj) {
        var factor = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("table").rows[rowIndex].children[0].value;

        $.post("jq_ajax.php", {updateRoundingFactor: "", roundingFactor: factor, itemId: itemId});
    }

    function updateItemDeviation(obj) {
        var deviation = obj.value;
        if (deviation < 0 ) {
            deviation = Math.abs(obj.value);
            obj.value = deviation;
        }
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("table").rows[rowIndex].children[0].value;

        $.post("jq_ajax.php", {updateItemDeviation: "", deviation: deviation, itemId: itemId});
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
            var tsiId = document.getElementById("table").rows[rowIndex].children[4].value;
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
        var itemId = document.getElementById("table").rows[rowIndex].children[0].value;

        $.post("jq_ajax.php", {updateItemQuantity: "", itemId: itemId, quantity: quantity});
    }

    function updateItem(obj) {
        var row =document.getElementById("table").rows[obj.parentNode.parentNode.rowIndex];
        var itemName = row.children[2].children[0].value;
        var itemUnit  = row.children[3].children[0].value;
        var itemPrice  = row.children[5].children[0].value;
        var itemId  = row.children[0].value;
        $.post("jq_ajax.php", {updateItems: "", itemName: itemName, itemUnit: itemUnit, itemId: itemId, itemPrice: itemPrice});
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

        $(".popup_close").click(function() {
            $(".main_iframe").removeClass("blur");
            $(".div_popup_back").fadeOut(190, "linear");
        });

        $("#add_item_button").click(function() {
            if ($(this).html() == "Item List") {
                $("#item_list_div").css("flex", "1");
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

            if (itemName) {
                $.post("jq_ajax.php", {addItem: "", itemName: itemName, itemUnit: itemUnit, itemQuant: itemQuantity}, function(data, status) {
                    $("body").append(data);
                    getTab(document.getElementById("day_tab"));
                    $("userinput").trigger("reset");
                });
            }
        });

        $(document).on("click", "#delete_item", function() {
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
            $(".tab_li").removeClass("disabled");
            $(".tab_add_button").removeClass("disabled");
            $(".tab_li.selected").css("pointer-events", "block");
        });

        $("#item_list_cancel").click(function() {
            $("#item_list_div").css("flex", "0");
        });

        $("#select_all").change(function() {
            $("input[type='checkbox']").prop("checked", $(this).prop("checked"));
        });

        $(document).on("click", "input.incorrect", function() {
            $(this).removeClass("incorrect");
        });
    });
</script>
