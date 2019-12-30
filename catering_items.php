<?php
session_start();
require_once "database/catering_item_table.php";
require_once "database/variables_table.php";

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

if (isset($_POST["catering_people"])) {
    VariablesTable::update_catering_people($_POST["catering_people"]);
}
if (isset($_POST["checkbox"])) {
    CateringItemTable::delete_multiple_items($_POST["checkbox"], $_SESSION["date"]);
}
$item_table = CateringItemTable::get_items_categories($_SESSION["date"]);
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
                    <div class="block" id="item_add_div" >
                        <input type="submit" value="Add Item" class="button button_add_drawer" id="item_add_button">
                    </div>
                </div>
            </div>
            <button id="drawer_tag_item" class="drawer_tag_open">Close</button>
        </div>
        <div class="div_fade"></div>

        <div class="div_table font_roboto" id="items_div_table">
            <div id="div_print_table">
                <table class="table_view" id="item_table_view" border="1px" >
                    <tr class="table_option_bar">
                        <th colspan="2" id="button_th">
                            <button class="button_flat entypo-plus" id="add_item_button">Add</button>
                            <div class="divider"></div>
                            <button class="button_flat entypo-trash" id="delete_item">Delete</button>
                        </th>
                        <th colspan="3" id="th_sales">
                            <div class="none" id="div_quantity_sales">
                               Number of People
                                <form action="catering_items.php" method="post" class="inline">
                                    <input type="number" name="catering_people" value="<?php echo VariablesTable::get_catering_people(); ?>" onchange="this.form.submit()" class="align_center">
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
                        <th id="th_rounding">Rounding</th>
                    </tr>
                    <tbody id="item_tbody">
                    <?php  $current_category = 1;?>
                    <?php mysqli_data_seek($item_table, 0); ?>
                    <?php  while($row = $item_table->fetch_assoc()): ?>
                    <?php  if ($row["category_name"] != $current_category AND $row["category_name"] != null): ?>
                            <?php $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="6" class="table_heading"><?php echo $row["category_name"]?><span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php elseif ($row["category_name"] != $current_category AND $row["category_name"] == null): ?>
                    <?php  $current_category = $row["category_name"];?>
                            <tr class="item_category_tr">
                                <td id="category" colspan="6" class="table_heading">Uncategorized Items<span class="arrow_down float_right collapse_arrow"></span></td>
                            </tr>
                    <?php endif ?>
                        <tr>
                            <input type="hidden" class="item_id" name="item_id" value="<?php echo $row["id"]?>">
                            <td class="td_checkbox">
                                <div class="checkbox">
                                    <input type="checkbox" class="item_checkbox" name="checkbox[]" value="<?php echo $row["id"]?>" form="checkbox_form">
                                    <span class="checkbox_style"></span>
                                </div>
                            </td>
                            <td><input type="text" name="item_name" value="<?php echo $row["name"]?>" onchange=updateItem(this) class="align_center item_name"></td>
                            <td><input type="text" name="item_unit" value="<?php echo $row["unit"]?>" onchange=updateItem(this) class="align_center"></td>
                            <td><input type="number" name="item_quantity" step="any" min="0" value="<?php echo $row["base_quantity"]?>" onchange=quantityChange(this) class="align_center number_view"></td>
                            <td>$<input type="number" name="item_price" step="any" min="0" value="<?php echo $row["price"]?>" onchange=updateItem(this) class="align_center number_view"></td>
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

    <form action="catering_items.php" method="post" id="checkbox_form"></form>
    <form action="catering_items.php" method="post">
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
    function  getTab() {
        $.post("jq_ajax.php", {getCateringItems: ""}, function(data, status) {
            document.getElementById("item_tbody").innerHTML = data;
            $("#add_item_button").html("Add");
            if (!$("#delete_item").length) {
                var deleteButton = '<button id="delete_item" class="button_flat entypo-trash">Delete</button>';
                $("#button_th").append(deleteButton);
            }
            $("#th_sales").attr("colspan", "3");
            $("#div_quantity_sales").css("display", "block");
            $("#th_quantity").html("Quantity");
            $("#th_deviation").css("display", "table-cell");
            $("#th_price").css("display", "table-cell");
            $("#th_rounding").css("display", "table-cell");
            $(".divider").show();
        });
    }

    function updateItem(obj) {
        var row =document.getElementById("item_table_view").rows[obj.parentNode.parentNode.rowIndex];
        var itemName = row.children[2].children[0].value;
        var itemUnit  = row.children[3].children[0].value;
        var itemPrice  = row.children[5].children[0].value;
        var itemId  = row.children[0].value;
        $.post("jq_ajax.php", {updateCateringItems: "", itemName: itemName, itemUnit: itemUnit, itemId: itemId, itemPrice: itemPrice}, function(data) {
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

    function quantityChange(obj) {
        var quantity = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[0].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[2].children[0].value;

        $.post("jq_ajax.php", {updateCateringItemQuantity: "", itemId: itemId, quantity: quantity}, function(data) {
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

    function updateRoundingOption(obj) {
        var option = obj.value;
        var rowIndex = obj.parentNode.parentNode.rowIndex;
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[0].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[2].children[0].value;

        $.post("jq_ajax.php", {updateCateringRoundingOption: "", roundingOption: option, itemId: itemId}, function(data) {
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
        var itemId = document.getElementById("item_table_view").rows[rowIndex].children[0].value;
        var itemName = document.getElementById("item_table_view").rows[rowIndex].children[2].children[0].value;

        $.post("jq_ajax.php", {updateCateringRoundingFactor: "", roundingFactor: factor, itemId: itemId}, function(data) {
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

        $(document).on("click", ".item_category_tr", function() {
            $(this).nextUntil(".item_category_tr").toggle();
            if ($(this).find("span").hasClass("up")) {
                $(this).find("span").removeClass("up").css("transform", "rotate(45deg)");
            } else {
                $(this).find("span").addClass("up").css("transform", "rotate(225deg)")
            }
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
            var itemQuant = $("#new_item_quantity").val();
            var itemUnit = $("#new_item_unit").val();
            var itemPrice = $("#new_item_price").val() == "" ? 'NULL' : $("#new_item_price").val();

            if (itemName != "" && itemUnit != "" && itemQuant != "") {
                $.post("jq_ajax.php", {addCateringItem: "", itemName: itemName, itemUnit: itemUnit, 
                                      itemQuant: itemQuant, itemPrice: itemPrice}, function(data, status) {
                    if (data == "item added") {
                        alertify
                            .delay(2500)
                            .success("Item added successfully");
                        getTab();
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
            $(".tr_confirm").css("display", "table");
            $(".checkbox").css("display", "block");
        });

        $("#td_done").click(function() {
            document.getElementById("checkbox_form").submit();
        });

        $("#td_cancel").click(function() {
            $(".tr_confirm").css("display", "none");
            $(".checkbox").css("display", "none");
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
    });
</script>
