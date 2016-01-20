<?php
    include "sql_common.php";
    session_start();
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
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
    <?php $date = date("Y-m-d"); ?>
    <div>
        <a href="category_status.php" class="buttonBack">Back</a>
    <div>
    <table>
        <tr>
            <th colspan="5"><?php echo date('D, M d Y', strtotime($date)); ?></th>
        </tr>
        <?php $current_category = null;
              $result = print_preview(); ?>
        <?php while ($row =$result->fetch_assoc()): ?>
            <?php if ($row["category_name"] != $current_category): ?>
                <?php $current_category = $row["category_name"] ?>
                <tr><th colspan="5"><?php echo $current_category ?></th></tr>
                <tr>
                    <th>Item</th>
                    <th>Unit</th>
                    <th>Quantity Present</th>
                    <th>Quantity Required</th>
                    <th>Notes</th>
                </tr>
            <?php endif ?>
            <tr>
                <?php $sales_factor = get_expected_sales() / get_base_sales(); ?>
                <td><?php echo $row["item_name"] ?></td>
                <td><?php echo $row["unit"] ?></td>
                <td><?php echo $row["quantity"] ?></td>
                <td><?php echo (is_numeric($row["quantity"]) ? get_estimated_quantity($sales_factor, $row["item_name"]) - (int)$row["quantity"] : "-") ?></td>
                <td><?php echo $row["notes"] ?></td>
            </tr>
        <?php endwhile ?>
    </table>
    
</body>
</html>