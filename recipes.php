<?php
session_start();
require_once "database/recipe_table.php";
require_once "database/item_table.php";
require_once "database/recipe_item_table.php";

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

if (isset($_POST["add_button"]) AND !empty($_POST["recipe"])) {
    try {
        if (!RecipeTable::add_recipe($_POST["recipe"])) {
            echo '<div class="error">Recipe already exists</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}
if(isset($_POST["delete_id"])) {
    RecipeTable::remove_recipe($_POST["delete_id"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main_iframe">
        <div class="div_category">
            <h4>Recipes</h4><hr>
            <div class="div_list_category">
                <ul class="category_list" id="recipe_list">
                <?php $result = RecipeTable::get_recipes($date = date('Y-m-d')) ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li id="<?php echo $row['id']?>" class="list_category_li" onclick=recipeSelect(this)>
                        <div class="handle_delete"><img src="images/delete.png" alt="" width="30px" height="30px"></div>
                        <span><?php echo $row["name"]?></span>
                        <form action="recipes.php" method="post">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']?>" >
                        </form>
                    </li>
                <?php endwhile ?>
                </ul>
            </div>
            <input type="hidden" id="category_select">
            <div class="category_add" id="category_add">
                <button class="button" onclick=slideDrawer()>Add</button>
            </div>
            <div class="category_add_drawer">
                <form action="recipes.php" method="post" >
                    <input class="category_input" type="text" name="recipe" id="category_name" placeholder="Recipe Name">
                    <input type="submit" name="add_button" value="Add" class="button">
                </form>
                <button class="button_cancel" onclick=slideDrawer()>cancel</button>
            </div>
            <div class="category_delete">
                <span class="span_delete">DELETE</span>
                <span class="span_hint"><div class="arrow_down"></div>DRAG HERE<div class="arrow_down"></div></span>
            </div>
        </div>

        <div class="list_container" id="list_container">
            <div class="div_item_list">
                <h4>Items in Recipe</h4><hr>
                <div id="div" class="div_list">
                    <ul class="category_list" name="" id="categorized_list" ></ul>
                </div>
            </div>
            <div class="div_item_list">
                <h4>Items List</h4><hr>
                <div class="div_list">
                    <ul class="category_list" >
                    <?php $result = ItemTable::get_items(); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="list_li all_items" id="list_li" item-id="<?php echo $row['id'] ?>"><?php echo $row["name"];?></li>
                    <?php endwhile ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script src="https://cdn.rawgit.com/alertifyjs/alertify.js/v1.0.10/dist/js/alertify.js"></script>
<script
      src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
      integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
      crossorigin="anonymous"></script>
<script>

    function recipeSelect(obj) {
        var recipeId = $(obj).attr("id");

        $.post("jq_ajax.php", {getRecipeItems: "", recipeId: recipeId}, function(data, status) {
            document.getElementById("div").innerHTML = data;
            $(".all_items").removeClass("selected");
            $(".recipe_item").each(function() {
                var itemName = $(this).attr("item-name");
                $(".all_items").each(function() {
                    if ($(this).html() == itemName) {
                        $(this).addClass("selected");
                    }
                });
            });
        });
    }

    function addRecipeItem(obj) {
        var recipeId = $(".list_category_li.active").attr("id");
        var itemId = $(obj).attr("item-id");

        $.post("jq_ajax.php", {addRecipeItem: "", itemId: itemId, recipeId: recipeId});
    }

    function deleteRecipeItem(obj) {
        var recipeId = $(".list_category_li.active").attr("id");
        var itemId = $(obj).attr("item-id");

        $.post("jq_ajax.php", {deleteRecipeItem: "", itemId: itemId, recipeId: recipeId});
    }

    function updateQuantity(obj) {
        var quantity = obj.value;
        var recipeItemId = $(obj).parent().attr("recipe-item-id");

        $.post("jq_ajax.php", {updateRecipeInventoryQuantity: "", quantity: quantity, recipeItemId: recipeItemId});
    }

    function slideDrawer() {
        $(".category_add_drawer").slideToggle(180, "linear");
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            recipeSelect($(this)[0]);
           $(this).addClass("active");
        });

        $("#recipe_list li").draggable({
            scroll: false,
            revert: "invalid",
            handle: ".handle_delete",
            containment: ".div_list_category",
            zIndex: 500,
            helper: function(event, ui) {
                var helper = $(this).clone()
                helper.addClass("category_drag");
                $(this).css("opacity", "0");
                return helper;
            },
            start: function(event, ui) {
                $(".category_delete").slideToggle(180, "linear");
            },
            stop: function(event, ui) {
                $(this).css("opacity", "1");
                $(".category_delete").slideToggle(180, "linear");
            }
        });

        $(".category_delete").droppable({
            drop: function(event, ui) {
                alertify.confirm("Delete Recipe '"+$(ui.draggable).children("span").html()+"' ?", function() {
                    $(ui.draggable).fadeOut(100, "linear");
                    $(ui.draggable).children("form").submit();
                });
            }
        });

        $(".list_category_li").click(function() {
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
        });

        $(".list_category_li").hover(
            function() {
                $(".handle_delete", this).css("display", "block");
            },
            function() {
                $(".handle_delete", this).css("display", "none");
            }
        );

        $(".all_items").click(function() {
            $(this).toggleClass(function() {
                if ($(this).hasClass("selected")) {
                    deleteRecipeItem($(this)[0]);
                } else {
                    addRecipeItem($(this)[0]);
                }
                recipeSelect($(".list_category_li.active")[0]);
                return "selected";
            });
        });
    });

</script>
