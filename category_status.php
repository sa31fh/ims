<?php
    include "sql_common.php";
    session_start();
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
            <?php $result = get_categories($_SESSION["date"]);
                 while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><form action="update_inventory.php" method="post" target="ifram">
                            <input type="submit" name="category_name" value="<?php echo $row["name"]; ?>" class="button">
                            <input type="hidden" name="category_id" value="<?php echo $row["id"] ?>">
                            </form></td>
                        <td><?php echo get_updated_items_count($row['id'], $_SESSION["date"]). '/' .get_total_items($row['id'], $_SESSION["date"]) ?></td>
                    </tr>
            <?php  endwhile ?>
            </table>
        </div>

        <div class="inline"><iframe src="" id="new_iframe" name="ifram" scrolling="no" frameborder="0" onload=adjustHeight(id)></iframe></div>
        
        <?php if ($_SESSION["userrole"] == "admin"): ?>
            <div>
                <span ><strong>Expected Sales ($):</strong></span>
                <input type="number" value="<?php echo get_expected_sales() ?>" onchange=updateSales(this)>
            </div>
        <?php endif ?>   
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function adjustHeight(iframeID){
        var iframe = document.getElementById(iframeID);
        iframe.height = 0 + "px";
        var nHeight = iframe.contentWindow.document .body.scrollHeight;
        iframe.height = (nHeight + 60) + "px";
    }

    function updateSales(obj){
        var sales = obj.value;

        $(function(){
            $.post("jq_ajax.php", {sales: sales});
        });
    }
</script>

