<nav>
    <ul class="clearfix">
        <li><a href="category_status.php">Home</a></li>
        <li><a href="edit_categories.php">Categories</a></li>
        <li><a href="edit_items.php">Items</a></li>
        <li><a href="manage_users.php">Users</a></li>
        <li><a href="messages.php">Messages</a></li>
    </ul>
    <ul id="nav_right">
        <li><a href="print_preview.php">Print Preview</a></li>
        <li id="userinfo" >
            <div id="username"><span><?php echo $_SESSION["username"]; ?></span></div>
            <div id="userrole"><span id="two"><?php echo $_SESSION["userrole"];?></span></div>
            <ul>
                <li><a href="change_password.php">Password</a></li>
            </ul>
        </li>
        <li id="logout">    
            <form action="new_nav.php" method="post">
                <input type="submit" name="logout" value="logout" class ="button">
            </form>
        </li>
    </ul>
</nav>

<?php 
    if (isset($_POST["logout"])) {
        session_start();
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }
?>