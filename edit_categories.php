<?php
session_start();
require_once "database/category_table.php";
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
?>
    <script>
        window.parent.location.href = window.parent.location.href;
    </script>
<?php
exit();
}
$_SESSION["last_activity"] = time();

if (isset($_POST["new_name"]) AND !empty($_POST["new_name"])) {
    try {
        if (!CategoryTable::add_category($_POST["new_name"], $_SESSION["date"])) {
            echo '<div class="error">Category already exists</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}

if (isset($_POST["edit_name"]) AND !empty($_POST["edit_name"])) {
    CategoryTable::update_category_name($_POST["edit_name"], $_POST["edit_id"]);
}
if(isset($_POST["delete_id"])) {
    CategoryTable::remove_category($_POST["delete_id"], $_SESSION["date"]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="category_main font_open_sans">
        <div class="div_category">
            <div class="div_list_title">
                <h4 class="font_roboto">Categories</h4>
                <span class="list_sort fa-sort-alpha-asc" id="cat_sort"></span>
            </div>
            <div class="div_list_category">
            <ul class="category_list" id="category_list">
                <?php $result = CategoryTable::get_categories($_SESSION["date"]) ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li id="<?php echo $row['id']?>" class="list_category_li" onclick=categorySelect(this)>
                        <span><?php echo $row["name"]?></span>
                        <form action="edit_categories.php" method="post">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']?>" >
                        </form>
                    </li>
                <?php endwhile ?>
            </ul>
            </div>
            <input type="hidden" id="category_select">
            <div class="option_bar" id="category_add">
                <div class="toolbar_div option" onclick=deleteCategory()>
                    <span class="icon_small entypo-trash"></span>
                    <span class="icon_small_text">delete</span>
                </div>
                <div class="toolbar_div option" onclick='slideDrawer("add")'>
                    <button class="button_round entypo-plus"></button>
                </div>
                <div class="toolbar_div option" onclick='slideDrawer("edit")' >
                    <span class="icon_small fa-edit"></span>
                    <span class="icon_small_text">edit</span>
                </div>
            </div>
            <div class="category_add_drawer">
                <input class="category_input" type="text" name="category" id="category_name" placeholder="Category Name">
                <button name="add_button" id="add_button" class="button" onclick=checkButton(this)>Add</button>
                <button class="button_cancel"  onclick=slideDrawer("")>cancel</button>
            </div>
            <div class="category_delete">
                <button class="button_flat" onclick=closeDelete()>done</button>
                <span class="span_delete">
                    <span class="span_hint entypo-trash">drag here to delete</span>
                </span>
            </div>
        </div>

        <div class="list_container" id="list_container">
            <div class="div_item_list">
                <div class="div_list_title">
                    <h4 class="font_roboto">Categorized Items</h4>
                    <span class="list_sort fa-sort-alpha-asc" id="cat_item_sort"></span>
                </div>
                <div id="div" class="div_list">
                    <ul class="category_list" name="" id="categorized_list" ></ul>
                </div>
            </div>
            <div class="div_item_list">
                <h4 class="font_roboto">Uncategorized Items</h4>
                <div class="div_list">
                    <ul class="category_list" name="select_uncat" id="uncategorized_list" >
                    <?php $result = ItemTable::get_uncategorized_items($_SESSION["date"]); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="list_li" id="<?php echo $row['id'] ?>" item-name="<?php echo $row['name'] ?>"><?php echo $row["name"];?></li>
                    <?php endwhile ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form action="edit_categories.php" method="post" id="add_form">
        <input type="hidden" name="new_name" id="new_name">
    </form>
    <form action="edit_categories.php" method="post" id="edit_form">
        <input type="hidden" name="edit_name" id="edit_name">
        <input type="hidden" name="edit_id" id="edit_id">
    </form>
    <input type="hidden" id="session_date" value="<?php echo $_SESSION["date"] ?>">
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script
      src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
      integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
      crossorigin="anonymous"></script>
<script src="touch_punch.js"></script>
<script>
    function categorySelect(obj) {
        var categoryName = obj.children[0].innerHTML;
        var date = $("#session_date").val() ;
        document.getElementById("category_select").value = obj.children[0].innerHTML;

        $.post("jq_ajax.php", {getCategorizedItems: categoryName, date: date}, function(data,status){
            document.getElementById("div").innerHTML = data;
            $("#categorized_list").sortable({
                delay: 50,
                revert: 120,
                containment: $("#categorized_list").parent().parent().parent(),
                connectWith: "#uncategorized_list",
                helper: function (event, ui) {
                    var helper = $('<li/>');
                    if (!ui.hasClass('selected')) {
                        ui.addClass('selected').siblings().removeClass('selected');
                    }
                    $("#uncategorized_list li").removeClass("selected");
                    var elements = ui.parent().children('.selected').clone();
                    ui.data('multidrag', elements).siblings('.selected').remove();
                    return helper.append(elements);
                },
                stop: function(event, ui) {
                    ui.item.after(ui.item.data('multidrag')).remove();
                },
                receive: function(event, ui) {
                    var categoryName = document.getElementById("category_select").value;
                    $(this).children(".selected").each(function(){
                        $.post("jq_ajax.php", {UpdateItemsCategory: "", itemName: $(this).attr("item-name"), categoryName: categoryName});
                    });
                },
                update: function(event, ui) {
                    ui.item.after(ui.item.data('multidrag')).remove();
                    var itemIds = $(this).sortable('toArray');
                    $.post("jq_ajax.php", {UpdateItemOrder: "", itemIds: itemIds});
                }
            }).on("click", "li", function () {
                $(this).toggleClass("selected");
                $("#uncategorized_list li").removeClass("selected");
            });
        });
    }

    function slideDrawer(type) {
        $(".category_add_drawer").slideToggle(120);
        switch (type) {
            case 'add':
                $("#category_name").val("").focus();
                $("#add_button").html("Add");
                break;
            case 'edit':
                $("#category_name").val($(".active").children("span").html()).focus();
                $("#add_button").html("Save");
        }
    }

    function deleteCategory() {
        alertify.confirm("Delete Category '"+$(".active").children("span").html()+"' ?", function() {
            $(".active").children("form").submit();
        });
    }

    function checkButton(obj) {
        switch ($(obj).html()) {
            case 'Add':
                addCategory();
                break;
            case 'Save':
                editCategory();
        }
    }

    function addCategory() {
        var ids = $(".list_category_li")
                    .map(function() {
                        return this.id;
                    }).get();
        $.post("jq_ajax.php", {UpdateCategoryOrder: "", categoryIds: ids});
        $("#new_name").val($("#category_name").val());
        $("#add_form").submit();
    }

    function editCategory() {
        $("#edit_name").val($("#category_name").val());
        $("#edit_id").val($(".active").attr("id"));
        $("#edit_form").submit();
    }

    $(document).ready(function() {

        $(".list_category_li:first").each(function() {
           categorySelect($(this)[0]);
           $(this).addClass("active");
        });

        $(".list_category_li").click(function() {
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
        });

        $("#uncategorized_list").sortable({
            delay: 50,
            revert: 120,
            containment: $("#uncategorized_list").parent().parent().parent(),
            connectWith: "#categorized_list",
            helper: function (event, ui) {
                var helper = $('<li/>');
                if (!ui.hasClass('selected')) {
                    ui.addClass('selected').siblings().removeClass('selected');
                }
                $("#categorized_list li").removeClass("selected");
                var elements = ui.parent().children('.selected').clone();
                ui.data('multidrag', elements).siblings('.selected').remove();
                return helper.append(elements);
            },
            stop: function(event, ui) {
                ui.item.after(ui.item.data('multidrag')).remove();
            },
            update: function(event, ui) {
                ui.item.after(ui.item.data('multidrag')).remove();
            },
            receive: function(event, ui) {
                $(this).children(".selected").each(function() {
                    $.post("jq_ajax.php", {UpdateItemsCategory: "", itemName: $(this).attr("item-name"), categoryName: null});
                });
            }
        });

        $("#category_list").sortable({
            revert: 150,
            containment: "#category_list",
            start: function(event, ui) {
                ui.item.addClass("category_drag");
            },
            stop: function (event, ui) {
                ui.item.removeClass("category_drag");
            },
            update: function(event, ui) {
                var ids = $(this).sortable("toArray");
                $.post("jq_ajax.php", {UpdateCategoryOrder: "", categoryIds: ids});
            }
        });

        $("#uncategorized_list").on('click', 'li', function() {
            $(this).toggleClass("selected");
            $("#categorized_list li").removeClass("selected");
        });

        $("#cat_sort").click(function() {
            alertify.confirm("Sort Categories alphabetically?", function() {
                $(".list_category_li").each(function() {
                    var item = $(this);
                    $(".list_category_li").each(function() {
                        if (item.find("span").html().toLowerCase() > $(this).find("span").html().toLowerCase()) {
                            $(this).insertBefore(item);
                        }
                    });
                });
                var ids = $(".list_category_li")
                    .map(function() {
                        return this.id;
                    }).get();
                $.post("jq_ajax.php", {UpdateCategoryOrder: "", categoryIds: ids});
            });
        });

        $("#cat_item_sort").click(function() {
            alertify.confirm("Sort Items alphabetically?", function() {
                $("#categorized_list").find(".list_li").each(function() {
                    var item = $(this);
                    $("#categorized_list").find(".list_li").each(function() {
                        if (item.html().toLowerCase() > $(this).html().toLowerCase()) {
                            $(this).insertBefore(item);
                        }
                    });
                });
                var ids = $("#categorized_list").find(".list_li")
                    .map(function() {
                        return this.id;
                    }).get();
                $.post("jq_ajax.php", {UpdateItemOrder: "", itemIds: ids});
            });
        });
    });
</script>
