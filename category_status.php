<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    if(!isset($_POST["dateview"])) {
            $date = date("Y-m-d");
    } else {$date = $_POST["dateview"];}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include_once "new_nav.php" ?>

    <div>
        <form action="category_status.php" method="post">
            <input type="date" name="dateview" onchange="this.form.submit()" value="<?php echo $date; ?>" >
        </form>
    </div>

    <div class="inline">
       <table border="1px">
        <tr><td colspan="2" ><?php echo date('D, M d Y', strtotime($date)); ?></td></tr>
        <tr>
            <th>Category</th>
            <th>Status</th>
        </tr>
        <?php $result = get_categories($date);
             while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><form action="update_inventory.php" method="post" target="ifram">
                        <input type="submit" name="category_name" value="<?php echo $row["name"]; ?>" class="button">
                        <input type="hidden" name="category_id" value="<?php echo $row["id"] ?>">
                        <input type="hidden" name="date" value="<?php echo $date ?>">
                        </form></td>
                    <td><?php echo get_updated_items_count($row['id'], $date). '/' .get_total_items($row['id'], $date) ?></td>
                </tr>
        <?php  endwhile ?>
        </table>
    </div>

    <div class="inline"><iframe src="" id="new_iframe" name="ifram" scrolling="no" frameborder="0" onload=adjustHeight(id)></iframe></div>
        
    <div>
        <span ><strong>Expected Sales ($):</strong></span>
        <input type="number" value="<?php echo get_expected_sales() ?>" onchange=updateSales(this)>
    </div>

</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    function adjustHeight(iframeID){
        var iframe = document.getElementById(iframeID);
        var nHeight = iframe.contentWindow.document .body.scrollHeight;

        iframe.height = (nHeight + 20) + "px";
    }

    function updateSales(obj){
        var sales = obj.value;

        $(function(){
            $.post("sql_common.php", {sales: sales});
        });
    }
</script>

