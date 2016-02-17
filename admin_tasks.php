<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION["userrole"] != "admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Tasks</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <ul class="sidenav" id="sideNav">
        <li id="heading"><span>Admin Tasks</span></li>
        <li><a class="active" href="edit_categories.php" target="task_frame" >Categories</a></li>
        <li><a href="edit_items.php" target="task_frame" >Items</a></li>
        <li><a href="manage_users.php" target="task_frame">Users</a></li>
    </ul>
    <?php include_once "new_nav.php" ?>

    <div class="main_messages">
        <iframe src="edit_categories.php" frameborder="0" name="task_frame" id="task_frame" scrolling="no" onload=adjustHeight(id) ></iframe>
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
    $(function() {
        $('#sideNav li a').click(function() {
           $('#sideNav li a').removeClass();
           $(this).addClass('active');
        });
     });
</script>