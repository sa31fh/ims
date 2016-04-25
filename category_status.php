<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_SESSION["date"])) {
    $_SESSION["date"] = date("Y-m-d");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php $page = "home";
          include_once "new_nav.php" ?>
    <div class="main">
        <div class="div_category" id="home_list">
           <ul class="category_list">
            <h4><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></h4><hr>
            <?php $result = CategoryTable::get_categories($_SESSION["date"]);
                 while ($row = $result->fetch_assoc()): ?>
                 <li class="list_category_li">
                    <form action="update_inventory.php" method="post" id="cat_form" target="ifram" class="inline">
                        <span><?php echo $row["name"]; ?></span>
                        <input type="hidden" name="category_name" value="<?php echo $row["name"] ?>">
                        <input type="hidden" name="category_id" value="<?php echo $row["id"] ?>">
                    </form>
                    <span id="item_counter"><?php echo ItemTable::get_updated_items_count($row['id'], $_SESSION["date"]). '/' .ItemTable::get_total_items($row['id'], $_SESSION["date"]) ?></span>
                 </li>
            <?php  endwhile?>
            </ul>
        </div>
        <div class="inline div_iframe_width"><iframe src="" id="new_iframe" name="ifram" scrolling="no" frameborder="0" onload=adjustHeight(id)></iframe></div>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function adjustHeight(iframeID) {
        var iframe = document.getElementById(iframeID);
        iframe.height = 0 + "px";
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }

    $(document).ready(function() {
        $(".list_category_li:first").each(function() {
            $(this).children().submit();
            $(this).addClass("active");
        });

        $(".list_category_li").click(function() {
            $(".list_category_li").removeClass("active");
            $(this).addClass("active");
            $(this).children().submit();
        });
    });
</script>
