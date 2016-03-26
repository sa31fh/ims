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
        <li id="heading"><h4>Admin Tasks</h4><hr></li>
        <li><a class="active" href="edit_categories.php" target="task_frame" >Categories</a></li>
        <li><a href="edit_items.php" target="task_frame" >Items</a></li>
        <li><a href="manage_users.php" target="task_frame">Users</a></li>
    </ul>
    <?php include_once "new_nav.php" ?>

    <div class="main_top_side">
        <iframe class="iframe_top" src="edit_categories.php" frameborder="0" name="task_frame" id="task_frame" onload=adjustHeight(id) ></iframe>
    </div>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    function adjustHeight(iframe){
        var iframe = document.getElementById(iframe);
        iframe.height = 0 + "px";
        iframe.height = (document.body.scrollHeight - 48) + "px";
    }
    $(function() {
        $('#sideNav li a').click(function() {
           $('#sideNav li a').removeClass();
           $(this).addClass('active');
        });
     });
</script>