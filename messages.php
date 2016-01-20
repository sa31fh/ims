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
    <?php include_once "new_nav.php" ?>

    <div>
        <form action="" method="post">
            <input type="text" name="recipient" placeholder="Recipient"><br/>
            <input type="text" name="title" placeholder="Title"><br/>
            <textarea name="message" id="" cols="30" rows="10" placeholder="Message"></textarea><br/>
            <input type="submit" value="Send" class="button">
        </form>
    </div>
    
</body>
</html>