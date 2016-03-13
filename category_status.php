<?php
session_start();
require_once "database/category_table.php";
require_once "database/item_table.php";
require_once "database/variables_table.php";

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
        <div class="inline">
           <table class="user_table">
            <tr><td colspan="2" ><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></td></tr>
            <tr>
                <th>Category</th>
                <th>Status</th>
            </tr>
            <?php $result = CategoryTable::get_categories($_SESSION["date"]);
                 while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><form action="update_inventory.php" method="post" target="ifram">
                            <input type="submit" name="category_name" value="<?php echo $row["name"]; ?>" class="button">
                            <input type="hidden" name="category_id" value="<?php echo $row["id"] ?>">
                            </form></td>
                        <td><?php echo ItemTable::get_updated_items_count($row['id'], $_SESSION["date"]). '/' .ItemTable::get_total_items($row['id'], $_SESSION["date"]) ?></td>
                    </tr>
            <?php  endwhile ?>
            </table>
        </div>
        <div class="inline div_iframe"><iframe src="" id="new_iframe" name="ifram" scrolling="no" frameborder="0" onload=adjustHeight(id)></iframe></div>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function adjustHeight(iframeID){
        var iframe = document.getElementById(iframeID);
        iframe.height = 0 + "px";
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }
</script>
