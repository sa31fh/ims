<?php
if (isset($_POST["logout"])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
if(isset($_POST["dateview"])) {
    $_SESSION["date"] = $_POST["dateview"];
}
?>

<nav class="nav_bar">
    <ul class="clearfix">
        <li class=<?php if (isset($page) AND ($page == "home")) {echo "active";} ?>><a href="category_status.php">Home</a></li>
        <li class=<?php if (isset($page) AND ($page == "messages")) {echo "active";} ?>><a href="messages.php">Messages</a></li>
    </ul>
    <ul id="nav_right">
        <li><div id="date_display"><?php echo date('D, M d Y', strtotime($_SESSION["date"])); ?></div>
            <ul id="nav_date">
                <li id="timezone_li"><span id="timezone"><?php echo $_SESSION["timezone"]; ?></span></li>
                <li><form action="" method="post"><input type="date" name="dateview" onchange="this.form.submit()" value="<?php echo $_SESSION["date"] ?>" ></form></li>
            </ul>
        </li>
        <li><a href="print_preview.php">Print Preview</a></li>
        <li id="userinfo" >
            <div id="username"><span><?php echo $_SESSION["username"]; ?></span></div>
            <ul>
                <li id="userrole_li"><div id="userrole"><?php echo $_SESSION["userrole"] ?></div></li>
                <li><a href="user_account.php">Account</a></li>
                <?php if ($_SESSION["userrole"] == "admin"): ?>
                    <li><a href="admin_tasks.php">Admin Tasks</a></li>
                <?php endif ?>
            </ul>
        </li>
        <li id="logout">
            <form action="new_nav.php" method="post">
                <input type="submit" name="logout" value="logout" class ="button">
            </form>
        </li>
        <li id="burger_list">
            <a >&#9776;</a>
            <ul>
                <li><a href="user_account.php">Account</a></li>
                <?php if ($_SESSION["userrole"] == "admin"): ?>
                    <li><a href="admin_tasks.php">Admin Tasks</a></li>
                <?php endif ?>
                <li>
                    <form action="new_nav.php" method="post">
                        <input type="submit" name="logout" value="logout" class ="button">
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.js"></script>
<script>
    $(document).ready(function(){
        $("nav ul li").hover(function(){
            $("ul", this).slideDown(150, "linear");
        }, function(){
            $("ul", this).slideUp(150, "linear");
        });
    });
</script>
