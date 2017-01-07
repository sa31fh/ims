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
if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] + $_SESSION["time_out"] * 60 < time()) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
$_SESSION["last_activity"] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Tasks</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="overflow_hidden">
    <div class="main">
        <ul class="sidenav font_roboto" id="sideNav">
            <li id="heading"><h4>Admin Tasks</h4></li>
            <li><a class="entypo-archive active" href="edit_categories.php" target="task_frame" >Categories</a></li>
            <li><a class="entypo-basket" href="edit_items.php" target="task_frame" >Items</a></li>
            <li><a class="entypo-book-open" href="recipes.php" target="task_frame" >Recipes</a></li>
            <li><a class="entypo-users" href="manage_users.php" target="task_frame">Users</a></li>
        </ul>

        <div class="main_top_side">
            <iframe class="iframe" src="edit_categories.php" frameborder="0" name="task_frame" id="task_frame"></iframe>
        </div>
    </div>
    <?php $page = "admin tasks";
    include_once "new_nav.php" ?>
</body>
</html>

<script type="text/javascript" src="//code.jquery.com/jquery-2.2.0.min.js"></script>
<script>
    $(document).ready(function() {

        $('#sideNav li a').click(function() {
           $('#sideNav li a').removeClass("active");
           $(this).addClass('active');
        });
     });
</script>
