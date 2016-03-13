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
if (isset($_POST["add_button"]) AND !empty($_POST["category"])) {
    try {
        if (!CategoryTable::add_category($_POST["category"])) {
            echo '<div class="error">Category already exists</div>'; 
        }
    } catch (Exception $e) {
        echo '<div class="error">'.$e->getMessage().'</div>';
    }
} 
if(isset($_POST["delete_button"]) AND !empty($_POST["category"])){
    CategoryTable::remove_category($_POST["category"]);
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
    <div>
        <form action="edit_categories.php" method="post" >
            <input class="userinput" type="text" name="category" id="category_name" placeholder="Category">
            <input type="submit" name="add_button" value="Add" class="button">
            <input type="submit" name="delete_button" id="delete_button" value="Delete" class="button" >
        </form>
    </div>

    <div class="category_div">
        <h5>Categories</h5>
        <form action="edit_categories.php" method="get">
            <select class="category_select" name="options" id="category_select" size="10" onchange=categorySelect(this);>
                <?php $result = CategoryTable::get_categories($date = date('Y-m-d')) ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['name']; ?>" > <?php echo $row["name"] ?> </option>
                <?php endwhile ?>
            </select>
        </form>
    </div>

    <div class="category_div">
        <h5>Uncategorized Items</h5>
        <form action="edit_categories.php" method="get">
            <select class="category_select" name="select_uncat" id="uncategorized_list" size="10" >
                <?php $result = ItemTable::get_uncategorized_items(); ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <option value="<?php echo $row['name']; ?>"> <?php echo $row["name"]; ?></option>
                <?php endwhile ?>
            </select>
        </form>
        <input type="button" value="Categorize ->" id="categorize_button" class="button">
    </div>

    <div class="category_div">
        <h5>Categorized Items</h5>
        <div id="div" class="none">
            <select class="category_select" name="" id="" size=8 ></select>
        </div>
        <input type="button" value="<- Uncategorize" id="uncategorize_button" class="button">
    </div>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function categorySelect(obj){
        var categoryName = obj.value;
        document.getElementById("category_name").value = obj.value;

        $(function(){
            $.post("jq_ajax.php", {showCategorizedItems: categoryName}, function(data,status){
                 document.getElementById("div").innerHTML = data;
            });
        });
    }

    $(function(){
        $("#categorize_button").click(function(){
            var unCategorizeValue = document.getElementById("uncategorized_list").value;
            var categoryName = document.getElementById("category_select").value;

            $.post("jq_ajax.php", {items: unCategorizeValue, categoryName: categoryName}, function(data,status){
            });
        
            $("#uncategorized_list > option:selected").each(function(){
                $(this).remove().appendTo("#categorized_list");
            });
        });
        
        $("#uncategorize_button").click(function(){
            var categorizeValue = document.getElementById("categorized_list").value;

            $.post("jq_ajax.php", {items: categorizeValue, categoryName: null}, function(data,status){
            });

            $("#categorized_list > option:selected").each(function(){
                $(this).remove().appendTo("#uncategorized_list");
            });
        });
    });
</script>




