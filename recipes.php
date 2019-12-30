<?php
session_start();
require_once "database/recipe_table.php";
require_once "database/catering_item_table.php";
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

if (isset($_POST["new_name"]) AND !empty($_POST["new_name"])) {
    try {
        if (!RecipeTable::add_recipe($_POST["new_name"], $_SESSION["date"])) {
            echo '<div class="error">Recipe already exists</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
}
if (isset($_POST["edit_name"]) AND !empty($_POST["edit_name"])) {
    RecipeTable::update_recipe($_POST["edit_name"], $_POST["edit_id"]);
}
if(isset($_POST["delete_id"])) {
    RecipeTable::remove_recipe($_POST["delete_id"], $_SESSION["date"]);
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
    <div class="main_iframe font_open_sans">
        <div class="div_category">
            <h4 class="font_roboto">Recipes</h4>
            <div class="div_list_category">
                <ul class="category_list" id="recipe_list">
                <?php $result = RecipeTable::get_recipes(date('Y-m-d')) ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li id="<?php echo $row['id']?>" class="list_category_li" onclick=recipeSelect(this)>
                        <span><?php echo $row["name"]?></span>
                        <form action="recipes.php" method="post">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']?>" >
                        </form>
                    </li>
                <?php endwhile ?>
                </ul>
            </div>
            <input type="hidden" id="category_select">
            <div class="option_bar" id="category_add">
                <div class="toolbar_div option" onclick=deleteRecipe()>
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
                <input class="category_input" type="text" name="recipe" id="category_name" placeholder="Recipe Name">
                <button name="add_button" id="add_button" class="button" onclick=checkButton(this)>Add</button>
                <button class="button_cancel"  onclick=slideDrawer("")>cancel</button>
            </div>
        </div>

        <div class="list_container" id="list_container">
            <div class="div_item_list">
                <h4 class="font_roboto">Items in Recipe</h4>
                <div id="div" class="div_list">
                    <ul class="category_list" name="" id="categorized_list" ></ul>
                </div>
            </div>
            <div class="div_item_list">
                <h4 class="font_roboto">Items List</h4>
                <div class="div_list">
                    <ul class="category_list" >
                    <?php $result = CateringItemTable::get_items($_SESSION["date"]); ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <li class="list_li all_items" id="list_li" item-id="<?php echo $row['id'] ?>"><?php echo $row["name"];?></li>
                    <?php endwhile ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <form action="recipes.php" method="post" id="add_form">
        <input type="hidden" name="new_name" id="new_name">
    </form>
    <form action="recipes.php" method="post" id="edit_form">
        <input type="hidden" name="edit_name" id="edit_name">
        <input type="hidden" name="edit_id" id="edit_id">
    </form>
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

    function addRecipe() {
        $("#new_name").val($("#category_name").val());
        $("#add_form").submit();
    }

    function editRecipe() {
        $("#edit_name").val($("#category_name").val());
        $("#edit_id").val($(".active").attr("id"));
        $("#edit_form").submit();
    }

    function deleteRecipe() {
        alertify.confirm("Delete Recipe '"+$(".active").children("span").html()+"' ?", function() {
            $(".active").children("form").submit();
        });
    }

    function checkButton(obj) {
        switch ($(obj).html()) {
            case 'Add':
                addRecipe();
                break;
            case 'Save':
                editRecipe();
        }
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            recipeSelect($(this)[0]);
           $(this).addClass("active");
        });

        $(".list_category_li").click(function() {
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
        });

        $(".all_items").click(function() {
                if ($(this).hasClass("selected")) {
                    deleteRecipeItem($(this)[0]);
                } else {
                    addRecipeItem($(this)[0]);
                }
            // $(this).toggleClass(function() {
            //     return "selected";
            // });
            recipeSelect($(".list_category_li.active")[0]);
        });
    });

</script>
